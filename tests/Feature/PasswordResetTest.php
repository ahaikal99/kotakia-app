<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Notification::fake();
    }

    public function test_login_and_recovery_forms_are_accessible(): void
    {
        $this->get('/signin')->assertOk()->assertSee('Lupa kata laluan?')->assertSee('data-password-toggle', false);
        $this->get(route('password.request'))->assertOk()->assertSee('Hantar pautan reset');
        $this->get(route('password.reset', ['token' => 'example', 'email' => 'test@example.com']))->assertStatus(410)->assertSee('noindex')->assertHeader('Referrer-Policy', 'no-referrer')->assertDontSee('Simpan kata laluan');
    }

    public function test_requests_hide_account_existence_and_throttle_duplicate_mail(): void
    {
        $user = User::factory()->create();
        $this->from('/forgot-password')->post(route('password.email'), ['email' => strtoupper($user->email)])->assertRedirect('/forgot-password')->assertSessionHas('status');
        $knownMessage = session('status');
        $this->post(route('password.email'), ['email' => 'unknown@example.com'])->assertSessionHas('status', $knownMessage);
        $this->post(route('password.email'), ['email' => $user->email]);
        Notification::assertSentToTimes($user, ResetPassword::class, 1);
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $this->assertNotSame($notification->token, DB::table('password_reset_tokens')->value('token'));
            $mail = $notification->toMail($user);
            $this->assertSame('receipts', $mail->mailer);
            $this->assertSame('kotakiahq@gmail.com', $mail->from[0]);
            $this->assertStringStartsWith(rtrim(config('app.url'), '/').'/reset-password/', $mail->viewData['url']);
            $this->assertStringContainsString('Reset kata laluan', $mail->render());

            return true;
        });
    }

    public function test_valid_token_changes_password_once_and_preserves_role(): void
    {
        $user = User::factory()->create(['role' => 'manager']);
        $oldRemember = $user->remember_token;
        $token = Password::createToken($user);
        $data = ['email' => $user->email, 'token' => $token, 'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!'];
        $this->post(route('password.update'), $data)->assertRedirect(route('login'))->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check($data['password'], $user->fresh()->password));
        $this->assertNotSame($oldRemember, $user->fresh()->remember_token);
        $this->assertSame('manager', $user->fresh()->role);
        $this->assertGuest();
        $this->assertDatabaseCount('password_reset_tokens', 0);
        $this->post(route('password.update'), $data)->assertSessionHasErrors('email');
        $log = AuditLog::where('action', 'password.reset_completed')->sole();
        $this->assertNotNull($log->ip_address);
        $this->assertStringNotContainsString($token, $log->toJson());
        $this->assertStringNotContainsString($data['password'], $log->toJson());
    }

    public function test_invalid_expired_and_mismatched_requests_cannot_change_password(): void
    {
        $user = User::factory()->create();
        $original = $user->password;
        $token = Password::createToken($user);
        $data = ['email' => $user->email, 'token' => $token, 'password' => 'NewPassword123!', 'password_confirmation' => 'wrong'];
        $this->post(route('password.update'), $data)->assertSessionHasErrors('password');
        $data['password_confirmation'] = $data['password'];
        $this->post(route('password.update'), [...$data, 'token' => 'invalid'])->assertSessionHasErrors('email');
        $this->post(route('password.update'), [...$data, 'email' => 'another@example.com'])->assertSessionHasErrors('email');
        $this->travel(61)->minutes();
        $this->post(route('password.update'), $data)->assertSessionHasErrors('email');
        $this->assertSame($original, $user->fresh()->password);
    }

    public function test_reset_request_rate_limit(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('password.email'), ['email' => 'nobody@example.com'])->assertRedirect();
        }
        $this->post(route('password.email'), ['email' => 'nobody@example.com'])->assertStatus(429);
    }

    public function test_link_expires_after_sixty_minutes_even_if_form_was_already_open(): void
    {
        $user = User::factory()->create();
        $original = $user->password;
        $token = Password::createToken($user);
        $url = route('password.reset', ['token' => $token, 'email' => $user->email]);
        $this->get($url)->assertOk()->assertSee('Simpan kata laluan')->assertHeader('Cache-Control', 'no-store, private');
        $this->travel(60)->minutes();
        $this->travel(1)->seconds();
        $this->get($url)->assertStatus(410)->assertSee('Mohon pautan baharu')->assertDontSee('Simpan kata laluan');
        $this->post(route('password.update'), ['token' => $token, 'email' => $user->email, 'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!'])->assertSessionHasErrors('email');
        $this->assertSame($original, $user->fresh()->password);
    }

    public function test_used_or_replaced_link_cannot_display_reset_form(): void
    {
        $user = User::factory()->create();
        $oldToken = Password::createToken($user);
        $token = Password::createToken($user);
        $this->get(route('password.reset', ['token' => $oldToken, 'email' => $user->email]))->assertStatus(410);
        $this->post(route('password.update'), ['token' => $token, 'email' => $user->email, 'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!'])->assertRedirect(route('login'));
        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))->assertStatus(410);
    }
}
