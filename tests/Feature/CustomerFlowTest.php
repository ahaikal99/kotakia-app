<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function cardData(): array
    {
        return [
            'title' => 'Majlis Arif & Najihah', 'theme' => 'perkahwinan',
            'host_name' => 'Keluarga Arif', 'celebrant_name' => 'Arif & Najihah',
            'event_date' => today()->addMonth()->format('Y-m-d'), 'start_time' => '11:00', 'end_time' => '16:00',
            'venue' => 'Dewan Melati', 'address' => 'Jalan Melati, Kuala Lumpur',
            'map_url' => 'https://maps.google.com/', 'contact_name' => 'Arif',
            'contact_phone' => '0123456789', 'message' => 'Jemput hadir ke majlis kami.',
        ];
    }

    public function test_customer_can_register_login_create_order_and_simulate_payment(): void
    {
        $this->get('/register')->assertOk();
        $this->post('/register', [
            'name' => 'Arif', 'phone' => '012-345 6789', 'email' => 'ARIF@example.com',
            'password' => 'secret12345', 'password_confirmation' => 'secret12345',
        ])->assertRedirect(route('login'));

        $user = User::firstOrFail();
        $this->assertSame('60123456789', $user->phone);
        $this->assertSame('arif@example.com', $user->email);
        $this->assertTrue(Hash::check('secret12345', $user->password));
        $this->assertGuest();

        $this->post('/signin', ['email' => 'ARIF@example.com', 'password' => 'secret12345'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->get('/dashboard')->assertOk()->assertSee('Cipta Kad');
        $this->get('/cards/create')->assertOk()->assertSee('Maklumat Majlis');
        $this->post('/cards', [...$this->cardData(), 'user_id' => 999, 'status' => 'demo_complete'])->assertRedirect();
        $card = $user->invitations()->firstOrFail();
        $this->assertSame('draft', $card->status);
        $this->assertDatabaseHas('invitations', ['id' => $card->id, 'user_id' => $user->id, 'message' => 'Jemput hadir ke majlis kami.']);

        $this->get(route('invitations.options', $card))->assertOk()->assertSee('Pakej Mawar');
        $this->put(route('invitations.options.save', $card), ['package_code' => 'orkid', 'design_code' => 'MD003', 'amount_cents' => 1])
            ->assertRedirect(route('payments.show', $card));
        $this->assertDatabaseHas('invitations', ['id' => $card->id, 'amount_cents' => 6900, 'design_code' => 'MD003', 'status' => 'awaiting_payment']);
        $this->get(route('payments.show', $card))->assertOk()->assertSee('RM69.00')->assertSee('Bayaran dalam talian belum tersedia');
        $this->post(route('payments.simulate', $card))->assertRedirect(route('payments.show', $card));
        $this->post(route('payments.simulate', $card))->assertRedirect();
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('payments', ['invitation_id' => $card->id, 'amount_cents' => 6900, 'status' => 'simulated', 'method' => 'dummy']);
        $this->assertDatabaseHas('invitations', ['id' => $card->id, 'status' => 'demo_complete']);
        $this->get(route('payments.show', $card))->assertOk()->assertSee('Tempahan berjaya direkodkan');
        $this->get('/dashboard')->assertOk()->assertSee('Tempahan direkodkan')->assertSee('Majlis Arif');
        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_guests_cannot_access_order_routes(): void
    {
        foreach (['/dashboard', '/order', '/cards/create', '/cards/1/edit', '/cards/1/options', '/cards/1/payment'] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
        $this->post('/cards', $this->cardData())->assertRedirect(route('login'));
        $this->post('/cards/1/payment')->assertRedirect(route('login'));
        $this->assertDatabaseCount('invitations', 0);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_customer_cannot_read_or_modify_another_customers_order(): void
    {
        $card = Invitation::factory()->awaitingPayment()->create();
        $this->actingAs(User::factory()->create());
        foreach (['edit', 'options', 'payment'] as $step) {
            $this->get('/cards/'.$card->id.'/'.$step)->assertNotFound();
        }
        $this->put('/cards/'.$card->id, $this->cardData())->assertNotFound();
        $this->put('/cards/'.$card->id.'/options', ['package_code' => 'mawar', 'design_code' => 'MD001'])->assertNotFound();
        $this->post('/cards/'.$card->id.'/payment')->assertNotFound();
        $this->get('/dashboard')->assertDontSee($card->title);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_registration_validation_and_duplicate_contact_details(): void
    {
        User::factory()->create(['email' => 'arif@example.com', 'phone' => '60123456789']);
        $this->post('/register', [
            'name' => 'Arif', 'email' => 'ARIF@example.com', 'phone' => '+60 12 345 6789',
            'password' => 'short', 'password_confirmation' => 'different',
        ])->assertSessionHasErrors(['email', 'phone', 'password']);
        $this->post('/register', [])->assertSessionHasErrors(['name', 'email', 'phone', 'password']);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_login_rejects_invalid_credentials_and_rate_limits_attempts(): void
    {
        User::factory()->create(['email' => 'arif@example.com']);
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/signin', ['email' => 'arif@example.com', 'password' => 'wrong'])->assertSessionHasErrors('email');
        }
        $this->post('/signin', ['email' => 'arif@example.com', 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_drafts_resume_and_require_valid_information_and_options(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->post('/cards', [...$this->cardData(), 'event_date' => today()->subDay()->format('Y-m-d'), 'end_time' => '10:00', 'map_url' => 'javascript:alert(1)'])
            ->assertSessionHasErrors(['event_date', 'end_time', 'map_url']);
        $this->assertDatabaseCount('invitations', 0);

        $card = Invitation::factory()->for($user)->create();
        $this->get(route('payments.show', $card))->assertRedirect(route('invitations.options', $card));
        $this->post(route('payments.simulate', $card))->assertStatus(409);
        $this->put(route('invitations.options.save', $card), ['package_code' => 'free', 'design_code' => 'MD999'])
            ->assertSessionHasErrors(['package_code', 'design_code']);
        $this->assertDatabaseCount('payments', 0);
        $this->get('/dashboard')->assertSee('Sambung Tempahan');
        $this->get(route('invitations.edit', $card))->assertOk()->assertSee($card->venue);

        $this->put(route('invitations.options.save', $card), ['package_code' => 'mawar', 'design_code' => 'MD001'])->assertRedirect();
        $this->put(route('invitations.update', $card), [...$this->cardData(), 'theme' => 'hari-jadi'])->assertRedirect(route('invitations.options', $card));
        $this->assertDatabaseHas('invitations', ['id' => $card->id, 'theme' => 'hari-jadi', 'package_code' => null, 'status' => 'draft']);
        $this->get(route('invitations.options', $card))->assertOk()->assertSee('Reka bentuk tema ini belum tersedia');
        $this->put(route('invitations.options.save', $card), ['package_code' => 'mawar', 'design_code' => 'MD001'])->assertSessionHasErrors('design_code');
    }

    public function test_completed_order_details_and_design_can_change_but_package_remains_locked(): void
    {
        $user = User::factory()->create();
        $card = Invitation::factory()->for($user)->awaitingPayment()->create();
        $this->actingAs($user)->post(route('payments.simulate', $card))->assertRedirect();
        $this->get(route('payments.show', $card))->assertOk()
            ->assertDontSee('Maklumat Kad')->assertDontSee('Pakej &amp; Design', false)
            ->assertSee('data-print-receipt', false)->assertSee('target="_blank"', false);
        $this->put(route('invitations.update', $card), [...$this->cardData(), 'venue' => 'Dewan Selepas Bayaran'])
            ->assertRedirect(route('payments.show', $card));
        $this->put(route('invitations.options.save', $card), ['package_code' => 'eksklusif', 'design_code' => 'MD002'])
            ->assertRedirect(route('payments.show', $card));
        $this->assertDatabaseHas('invitations', [
            'id' => $card->id, 'venue' => 'Dewan Selepas Bayaran', 'package_code' => 'mawar',
            'amount_cents' => 4900, 'design_code' => 'MD002', 'status' => 'demo_complete',
        ]);
        $this->assertDatabaseCount('payments', 1);
        $card->refresh();
        $this->post('/logout');
        $this->post('/signin', ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('dashboard'));
        $this->get('/dashboard')->assertOk()->assertSee($card->title)->assertSee('Tempahan direkodkan');
        $this->get(route('payments.show', $card))->assertOk()->assertSee('RM49.00');
        $this->assertDatabaseCount('payments', 1);
    }
}
