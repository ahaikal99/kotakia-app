<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_customer_can_update_name_email_and_password_but_not_phone(): void
    {
        $user = User::factory()->create([
            'name' => 'Nama Lama', 'email' => 'lama@example.test', 'phone' => '60123456789', 'password' => 'password',
        ]);
        $this->actingAs($user)->get(route('profile.edit'))->assertOk()
            ->assertSee($user->phone)->assertSee('Nombor telefon tidak boleh diubah');

        $this->put(route('profile.update'), [
            'name' => 'Nama Baharu', 'email' => ' BARU@EXAMPLE.TEST ', 'phone' => '60999999999',
            'current_password' => 'password', 'password' => 'rahsia-baharu', 'password_confirmation' => 'rahsia-baharu',
        ])->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertSame('Nama Baharu', $user->name);
        $this->assertSame('baru@example.test', $user->email);
        $this->assertSame('60123456789', $user->phone);
        $this->assertTrue(Hash::check('rahsia-baharu', $user->password));
        $this->assertDatabaseHas('audit_logs', ['action' => 'profile.updated', 'user_id' => $user->id]);
        $this->assertStringNotContainsString('rahsia-baharu', AuditLog::all()->toJson());
    }

    public function test_profile_rejects_duplicate_email_and_wrong_current_password(): void
    {
        $user = User::factory()->create(['email' => 'sendiri@example.test', 'password' => 'password']);
        User::factory()->create(['email' => 'orang@example.test']);

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Nama', 'email' => 'orang@example.test', 'current_password' => 'salah',
            'password' => 'baharu-12345', 'password_confirmation' => 'baharu-12345',
        ])->assertSessionHasErrors(['email', 'current_password']);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
