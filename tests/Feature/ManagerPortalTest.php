<?php

namespace Tests\Feature;

use App\Models\AccessControl;
use App\Models\AuditLog;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ManagerPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function registration(): array
    {
        return ['name' => 'New Manager', 'email' => 'new@example.test', 'phone' => '0123456789', 'password' => 'StrongTest123!', 'password_confirmation' => 'StrongTest123!'];
    }

    private function controlData(string $key, bool $blocked = true): array
    {
        $control = AccessControl::where('key', $key)->firstOrFail();

        return ['is_blocked' => $blocked ? '1' : '0', 'reason' => 'Penyelenggaraan berjadual', 'version' => hash('sha256', $control->getRawOriginal('updated_at').json_encode([$control->is_blocked, $control->reason, $control->updated_by]))];
    }

    public function test_public_registration_cannot_create_manager_and_login_redirects_by_role(): void
    {
        $this->post('/register', [...$this->registration(), 'role' => 'manager', 'is_active' => false])->assertRedirect(route('login'));
        $customer = User::where('email', 'new@example.test')->firstOrFail();
        $this->assertFalse($customer->isManager());
        $this->assertTrue($customer->is_active);
        $this->post('/signin', ['email' => $customer->email, 'password' => 'StrongTest123!'])->assertRedirect(route('dashboard'));
        $this->post('/logout');
        $manager = User::factory()->manager()->create();
        $this->post('/signin', ['email' => $manager->email, 'password' => 'password'])->assertRedirect(route('manager.index'));
        $this->get('/dashboard')->assertRedirect(route('manager.index'));
    }

    public function test_manager_routes_are_protected_against_guests_and_customers(): void
    {
        $this->get('/manager')->assertRedirect(route('login'));
        $this->post('/manager/users/managers', $this->registration())->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create());
        foreach (['/manager', '/manager/orders', '/manager/orders/1', '/manager/users', '/manager/users/1', '/manager/users/create-manager', '/manager/logs', '/manager/logs/1', '/manager/controls'] as $url) {
            $this->get($url)->assertForbidden();
        }
        $this->post('/manager/users/managers', $this->registration())->assertForbidden();
        $this->patch('/manager/users/1', ['is_active' => '1'])->assertForbidden();
        $this->put('/manager/controls/payments', $this->controlData('payments'))->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'new@example.test']);
        $this->assertNull(AccessControl::blocked('payments'));
    }

    public function test_manager_can_view_search_and_filter_orders_users_and_logs(): void
    {
        $manager = User::factory()->manager()->create();
        $customer = User::factory()->create(['name' => 'Pelanggan Dicari']);
        $order = Invitation::factory()->for($customer)->awaitingPayment()->create(['title' => 'Majlis Dicari']);
        $log = AuditLog::factory()->create(['user_id' => $customer->id, 'action' => 'invitation.created', 'ip_address' => '203.0.113.5']);
        $this->actingAs($manager);
        foreach (['/manager', '/manager/users/create-manager', '/manager/controls'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/manager/orders?q=Dicari&status=awaiting_payment')->assertOk()->assertSee('Majlis Dicari');
        $this->get('/manager/orders?q=no-match')->assertOk()->assertDontSee('Majlis Dicari');
        $this->get('/manager/orders/'.$order->id)->assertOk()->assertSee('Dewan Seri Indah');
        $this->get('/manager/users?q=Dicari&role=customer&active=1')->assertOk()->assertSee('Pelanggan Dicari')->assertDontSee($manager->email);
        $this->get('/manager/users/'.$customer->id)->assertOk()->assertSee('Majlis Dicari');
        $this->get('/manager/logs?ip=203.0.113.5&action=invitation.created')->assertOk()->assertSee('203.0.113.5');
        $this->get('/manager/logs?to='.today()->format('Y-m-d'))->assertOk();
        $this->get('/manager/logs/'.$log->id)->assertOk()->assertSee('invitation.created');
    }

    public function test_only_manager_can_add_manager_and_change_account_status(): void
    {
        $manager = User::factory()->manager()->create();
        $this->actingAs($manager)->post('/manager/users/managers', $this->registration())->assertRedirect();
        $new = User::where('email', 'new@example.test')->firstOrFail();
        $this->assertTrue($new->isManager());
        $this->assertTrue(Hash::check('StrongTest123!', $new->password));
        $this->assertDatabaseHas('audit_logs', ['action' => 'manager.account_created', 'user_id' => $manager->id, 'subject_id' => $new->id]);
        $this->patch('/manager/users/'.$manager->id, ['is_active' => '0', 'blocked_reason' => 'Test'])->assertSessionHasErrors('is_active');
        $this->assertTrue($manager->fresh()->is_active);
        $this->patch('/manager/users/'.$new->id, ['is_active' => '0'])->assertSessionHasErrors('blocked_reason');
        $this->patch('/manager/users/'.$new->id, ['is_active' => '0', 'blocked_reason' => 'Semakan akaun'])->assertRedirect();
        $this->assertFalse($new->fresh()->is_active);
        $this->post('/logout');
        $this->post('/signin', ['email' => $new->email, 'password' => 'StrongTest123!'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->actingAs($new->fresh())->get('/manager')->assertForbidden()->assertSee('Semakan akaun');
        $this->assertGuest();
        $this->actingAs($manager)->patch('/manager/users/'.$new->id, ['is_active' => '1'])->assertRedirect();
        $this->assertTrue($new->fresh()->is_active);
        $this->assertNull($new->fresh()->blocked_reason);
    }

    public function test_payment_block_applies_to_already_open_forms_and_does_not_create_payment(): void
    {
        $manager = User::factory()->manager()->create();
        $customer = User::factory()->create();
        $card = Invitation::factory()->for($customer)->awaitingPayment()->create();
        $this->actingAs($customer)->get(route('payments.show', $card))->assertOk();
        $this->actingAs($manager)->put('/manager/controls/payments', $this->controlData('payments'))->assertRedirect();
        $this->assertDatabaseHas('audit_logs', ['action' => 'manager.page_blocked', 'user_id' => $manager->id]);
        $this->actingAs($customer)->get(route('payments.show', $card))->assertStatus(503)->assertSee('Penyelenggaraan berjadual');
        $this->post(route('payments.simulate', $card))->assertStatus(503);
        $this->assertDatabaseCount('payments', 0);
        $this->assertSame('awaiting_payment', $card->fresh()->status);
        $this->actingAs($manager)->put('/manager/controls/payments', $this->controlData('payments', false))->assertRedirect();
        $this->actingAs($customer)->post(route('payments.simulate', $card))->assertRedirect();
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_login_block_preserves_manager_access_and_blocks_existing_customer_sessions(): void
    {
        $manager = User::factory()->manager()->create();
        $customer = User::factory()->create();
        $this->actingAs($manager)->put('/manager/controls/login', $this->controlData('login'))->assertRedirect();
        $this->get('/manager/controls')->assertOk();
        $this->post('/logout');
        $this->get('/signin')->assertOk()->assertSee('Penyelenggaraan berjadual');
        $this->post('/signin', ['email' => $customer->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->actingAs($customer)->get('/dashboard')->assertStatus(503);
        $this->post('/logout')->assertRedirect();
        $this->post('/signin', ['email' => $manager->email, 'password' => 'password'])->assertRedirect(route('manager.index'));
        $this->put('/manager/controls/login', $this->controlData('login', false))->assertRedirect();
        $this->post('/logout');
        $this->post('/signin', ['email' => $customer->email, 'password' => 'password'])->assertRedirect(route('dashboard'));
    }

    public function test_registration_and_order_controls_enforce_server_side_and_require_reason(): void
    {
        $manager = User::factory()->manager()->create();
        $this->actingAs($manager)->put('/manager/controls/registration', [...$this->controlData('registration'), 'reason' => ''])->assertSessionHasErrors('reason');
        $stale = $this->controlData('registration');
        $this->put('/manager/controls/registration', $stale)->assertRedirect();
        $this->put('/manager/controls/registration', [...$stale, 'is_blocked' => '0'])->assertSessionHasErrors('version');
        $this->put('/manager/controls/orders', $this->controlData('orders'))->assertRedirect();
        $this->post('/logout');
        $this->get('/register')->assertStatus(503);
        $this->post('/register', $this->registration())->assertStatus(503);
        $customer = User::factory()->create();
        $card = Invitation::factory()->for($customer)->create();
        $this->actingAs($customer)->get('/dashboard')->assertOk();
        foreach (['/cards/create', '/order', '/cards/'.$card->id.'/edit', '/cards/'.$card->id.'/options'] as $url) {
            $this->get($url)->assertStatus(503);
        }
        $this->post('/cards', [])->assertStatus(503);
        $this->put('/cards/'.$card->id, [])->assertStatus(503);
        $this->put('/cards/'.$card->id.'/options', [])->assertStatus(503);
        $this->assertDatabaseCount('invitations', 1);
    }

    public function test_first_manager_bootstrap_is_one_time_and_audited(): void
    {
        $user = User::factory()->create(['email' => 'first@example.test']);
        $this->artisan('manager:bootstrap', ['email' => $user->email])->assertSuccessful();
        $this->assertTrue($user->fresh()->isManager());
        $this->assertDatabaseHas('audit_logs', ['action' => 'manager.bootstrapped', 'subject_id' => $user->id]);
        $other = User::factory()->create();
        $this->artisan('manager:bootstrap', ['email' => $other->email])->assertFailed();
        $this->assertFalse($other->fresh()->isManager());
    }
}
