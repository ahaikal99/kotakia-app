<?php

namespace Tests\Feature;

use App\Mail\WelcomeMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WelcomeEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function registration(): array
    {
        return ['name' => 'Pelanggan Contoh', 'phone' => '0123456789', 'email' => 'customer@example.com', 'password' => 'Example12345!', 'password_confirmation' => 'Example12345!'];
    }

    public function test_registration_queues_one_branded_welcome_email(): void
    {
        Mail::fake();
        $this->post('/register', $this->registration())->assertRedirect(route('login'));
        Mail::assertQueued(WelcomeMail::class, function (WelcomeMail $mail) {
            $this->assertSame('database', $mail->connection);
            $this->assertSame('receipts', $mail->queue);
            $this->assertSame('receipts', $mail->mailer);
            $this->assertSame('kotakiahq@gmail.com', $mail->envelope()->from->address);
            $html = $mail->render();
            $this->assertStringContainsString('Logo Kotakia', $html);
            $this->assertStringContainsString('data:image/png;base64,', $html);
            $this->assertStringContainsString('Pelanggan Contoh', $html);
            $this->assertStringContainsString(rtrim(config('app.url'), '/').'/signin', $html);
            $this->assertStringNotContainsString('Example12345!', $html);

            return $mail->hasTo('customer@example.com');
        });
        $this->post('/register', $this->registration())->assertSessionHasErrors('email');
        Mail::assertQueuedCount(1);
    }

    public function test_welcome_delivery_is_durable_and_does_not_store_password_in_job(): void
    {
        $this->post('/register', $this->registration())->assertRedirect(route('login'));
        $this->assertDatabaseCount('jobs', 1);
        $job = DB::table('jobs')->first();
        $this->assertSame('receipts', $job->queue);
        $this->assertStringNotContainsString('Example12345!', $job->payload);
    }
}
