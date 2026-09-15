<?php

namespace Tests\Feature;

use App\Jobs\SendReceiptEmail;
use App\Mail\ReceiptMail;
use App\Models\AuditLog;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\User;
use App\Services\ReceiptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use RuntimeException;
use Tests\TestCase;

class ReceiptTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_payment_generates_one_receipt_and_one_durable_email_job(): void
    {
        $card = Invitation::factory()->awaitingPayment()->create();
        $this->actingAs($card->user)->post(route('payments.simulate', $card))->assertRedirect();
        $this->post(route('payments.simulate', $card))->assertRedirect();
        $this->assertDatabaseCount('receipts', 1);
        $this->assertDatabaseCount('jobs', 1);
        $this->assertDatabaseHas('jobs', ['queue' => 'receipts']);
        $receipt = Receipt::sole();
        $this->assertSame(4900, array_sum(array_column($receipt->details['items'], 'amount_cents')));
        $this->assertFalse($receipt->details['paid']);
        $this->assertSame('queued', $receipt->email_status);
    }

    public function test_receipt_snapshot_does_not_change_after_card_or_customer_edits(): void
    {
        $payment = Payment::factory()->create();
        $service = app(ReceiptService::class);
        $receipt = $service->issue($payment, false);
        $original = $receipt->details;
        $payment->invitation->update(['title' => 'Nama majlis baharu']);
        $payment->invitation->user->update(['email' => 'changed@example.test', 'name' => 'Nama Baharu']);
        $again = $service->issue($payment);
        $this->assertSame($receipt->id, $again->id);
        $this->assertSame($original, $again->details);
        $this->assertDatabaseCount('jobs', 0);
    }

    public function test_receipt_and_email_action_require_ownership_and_completed_payment(): void
    {
        $card = Invitation::factory()->awaitingPayment()->create();
        $this->get(route('receipts.show', $card))->assertRedirect(route('login'));
        $this->actingAs($card->user)->get(route('receipts.show', $card))->assertNotFound();
        Payment::factory()->for($card)->create();
        $this->actingAs(User::factory()->create())->get(route('receipts.show', $card))->assertNotFound();
        $this->post(route('receipts.send', $card))->assertNotFound();
        $this->actingAs($card->user)->get(route('receipts.show', $card))->assertOk()
            ->assertSee('Logo Kotakia')->assertSee('Setup kad')->assertSee('Termasuk dalam pakej')
            ->assertSee('Tiada wang dicaj')->assertDontSee('BAYARAN DITERIMA');
        $this->post(route('receipts.send', $card))->assertRedirect();
        $this->post(route('receipts.send', $card))->assertRedirect();
        $this->assertDatabaseCount('jobs', 1);
    }

    public function test_email_uses_gmail_sender_snapshot_and_skips_already_sent_receipts(): void
    {
        Queue::fake();
        Mail::fake();
        $payment = Payment::factory()->create(['status' => 'paid']);
        $receipt = app(ReceiptService::class)->issue($payment);
        Queue::assertPushed(SendReceiptEmail::class, 1);
        $mail = new ReceiptMail($receipt);
        $this->assertSame('kotakiahq@gmail.com', $mail->envelope()->from->address);
        $this->assertStringContainsString('Resit Bayaran', $mail->envelope()->subject);
        $html = $mail->render();
        $this->assertStringContainsString('BAYARAN DITERIMA', $html);
        $this->assertStringContainsString('Setup kad', $html);
        $this->assertStringContainsString('data:image/png;base64,', $html);
        (new SendReceiptEmail($receipt->id))->handle();
        (new SendReceiptEmail($receipt->id))->handle();
        Mail::assertSent(ReceiptMail::class, fn ($mail) => $mail->hasTo($receipt->details['customer_email']));
        Mail::assertSentCount(1);
        $this->assertSame('sent', $receipt->fresh()->email_status);
        $this->assertNotNull($receipt->fresh()->emailed_at);
        $this->assertDatabaseHas('audit_logs', ['action' => 'receipt.email_sent']);
    }

    public function test_failure_is_retryable_without_marking_email_as_sent(): void
    {
        $payment = Payment::factory()->create();
        $receipt = app(ReceiptService::class)->issue($payment, false);
        (new SendReceiptEmail($receipt->id))->failed(new RuntimeException('SMTP private-secret'));
        $this->assertSame('failed', $receipt->fresh()->email_status);
        $this->assertNull($receipt->fresh()->emailed_at);
        $this->actingAs($payment->invitation->user)->post(route('receipts.send', $payment->invitation))->assertRedirect();
        $this->assertSame('queued', $receipt->fresh()->email_status);
        $this->assertDatabaseCount('jobs', 1);
        $this->assertStringNotContainsString('private-secret', AuditLog::all()->toJson());
    }

    public function test_receipt_and_job_are_rolled_back_together(): void
    {
        $payment = Payment::factory()->create();
        DB::beginTransaction();
        app(ReceiptService::class)->issue($payment);
        $this->assertDatabaseCount('jobs', 1);
        DB::rollBack();
        $this->assertDatabaseCount('jobs', 0);
        $this->assertDatabaseCount('receipts', 0);
    }
}
