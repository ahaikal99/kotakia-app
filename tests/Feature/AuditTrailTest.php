<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use LogicException;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_auth_events_keep_actor_ip_and_never_store_credentials(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.8', 'HTTP_USER_AGENT' => 'Kotakia Audit Test']);
        $this->post('/register', [
            'name' => 'Audit Customer', 'phone' => '0123456789', 'email' => 'audit@example.test',
            'password' => 'secret-audit-123', 'password_confirmation' => 'secret-audit-123',
        ])->assertRedirect(route('login'));
        $user = User::firstOrFail();
        $registered = AuditLog::where('action', 'auth.registered')->sole();
        $this->assertSame($user->id, $registered->user_id);
        $this->assertSame('user', $registered->subject_type);
        $this->assertSame('203.0.113.8', $registered->ip_address);
        $this->assertSame('Kotakia Audit Test', $registered->user_agent);

        $this->post('/signin', ['email' => $user->email, 'password' => 'wrong-secret'])->assertSessionHasErrors();
        $failed = AuditLog::where('action', 'auth.login_failed')->sole();
        $this->assertSame('failed', $failed->outcome);
        $this->assertNull($failed->user_id);
        $this->assertSame('audit@example.test', $failed->metadata['attempted_email']);

        $this->post('/signin', ['email' => $user->email, 'password' => 'secret-audit-123'])->assertRedirect(route('dashboard'));
        $this->post('/logout')->assertRedirect();
        foreach (['auth.logged_in', 'auth.logged_out'] as $action) {
            $this->assertDatabaseHas('audit_logs', ['action' => $action, 'user_id' => $user->id, 'outcome' => 'success']);
        }
        $logs = AuditLog::all()->toJson();
        foreach (['secret-audit-123', 'wrong-secret', $user->password, 'remember_token', 'password_confirmation'] as $secret) {
            $this->assertStringNotContainsString($secret, $logs);
        }
    }

    public function test_mutations_capture_changes_and_repeated_payments_are_distinct(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $data = Invitation::factory()->make()->only([
            'title', 'theme', 'host_name', 'celebrant_name', 'start_time', 'end_time', 'venue', 'address', 'contact_name', 'contact_phone',
        ]);
        $data['event_date'] = today()->addMonth()->format('Y-m-d');
        $this->post('/cards', $data)->assertRedirect();
        $card = $user->invitations()->sole();
        $this->assertDatabaseHas('audit_logs', ['action' => 'invitation.created', 'subject_id' => $card->id, 'user_id' => $user->id]);

        $this->put(route('invitations.update', $card), [...$data, 'venue' => 'Dewan Baharu'])->assertRedirect();
        $updated = AuditLog::where('action', 'invitation.updated')->sole();
        $this->assertSame($data['venue'], $updated->metadata['before']['venue']);
        $this->assertSame('Dewan Baharu', $updated->metadata['after']['venue']);
        $this->assertArrayNotHasKey('title', $updated->metadata['after']);

        $this->put(route('invitations.options.save', $card), ['package_code' => 'orkid', 'design_code' => 'MD003', 'amount_cents' => 1])->assertRedirect();
        $options = AuditLog::where('action', 'invitation.options_selected')->sole();
        $this->assertSame(6900, $options->metadata['after']['amount_cents']);
        $this->post(route('payments.simulate', $card))->assertRedirect();
        $this->post(route('payments.simulate', $card))->assertRedirect();
        $this->assertSame(1, AuditLog::where('action', 'payment.simulated')->count());
        $this->assertSame(1, AuditLog::where('action', 'payment.simulation_repeated')->count());
        $payment = AuditLog::where('action', 'payment.simulated')->sole();
        $this->assertSame('payment', $payment->subject_type);
        $this->assertSame($card->payment->id, $payment->subject_id);
        $this->assertSame($card->id, $payment->metadata['after']['invitation_id']);
    }

    public function test_denials_invalid_input_and_unknown_routes_are_logged(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->assertDatabaseHas('audit_logs', ['action' => 'access.unauthenticated', 'outcome' => 'failed']);

        $card = Invitation::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('invitations.edit', $card))->assertNotFound();
        $denied = AuditLog::where('action', 'resource.not_found')->sole();
        $this->assertSame($user->id, $denied->user_id);
        $this->assertSame($card->id, $denied->subject_id);
        $this->assertNull($denied->metadata);

        $this->post('/cards', ['password' => 'do-not-log-me'])->assertSessionHasErrors();
        $invalid = AuditLog::where('action', 'validation.failed')->sole();
        $this->assertContains('title', $invalid->metadata['invalid_fields']);
        $this->assertSame('failed', $invalid->outcome);
        $this->get('/unknown-url?token=private-token')->assertNotFound();
        $this->assertStringNotContainsString('private-token', AuditLog::all()->toJson());
        $this->assertStringNotContainsString('do-not-log-me', AuditLog::all()->toJson());
    }

    public function test_ipv6_and_catalog_filters_are_recorded_without_trusting_spoofed_headers(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '2001:db8::10'])
            ->withHeaders(['X-Forwarded-For' => '198.51.100.99', 'Authorization' => 'Bearer private-key', 'User-Agent' => str_repeat('A', 1100)])
            ->get('/design?tema=perkahwinan&susun=nama-az&token=secret-query')->assertOk();
        $log = AuditLog::sole();
        $this->assertSame('2001:db8::10', $log->ip_address);
        $this->assertSame(1000, mb_strlen($log->user_agent));
        $this->assertSame('page.viewed', $log->action);
        $this->assertSame(['tema' => 'perkahwinan', 'susun' => 'nama-az'], $log->metadata);
        $this->assertSame('/design', $log->route_path);
        $this->assertStringNotContainsString('secret-query', $log->toJson());
        $this->assertStringNotContainsString('private-key', $log->toJson());
    }

    public function test_throttled_login_is_logged(): void
    {
        $email = 'limited@example.test';
        $key = 'login:'.hash('sha256', $email.'|127.0.0.1');
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($key, 60);
        }
        $this->post('/signin', ['email' => $email, 'password' => 'not-recorded'])->assertSessionHasErrors();
        $this->assertDatabaseHas('audit_logs', ['action' => 'auth.login_throttled', 'outcome' => 'failed']);
    }

    public function test_history_survives_account_deletion_and_model_rejects_changes(): void
    {
        $user = User::factory()->create();
        $log = AuditLog::factory()->create(['user_id' => $user->id]);
        $user->delete();
        $this->assertDatabaseHas('audit_logs', ['id' => $log->id, 'user_id' => $user->id]);
        try {
            $log->update(['action' => 'tampered']);
            $this->fail('Audit log updates must be rejected.');
        } catch (LogicException) {
            $this->assertSame('page.viewed', $log->fresh()->action);
        }
        $this->expectException(LogicException::class);
        $log->delete();
    }
}
