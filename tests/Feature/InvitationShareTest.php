<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationShareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_completed_invitation_has_copyable_link_and_whatsapp_message(): void
    {
        $user = User::factory()->create();
        $invitation = Invitation::factory()->for($user)->awaitingPayment()->create([
            'title' => 'Majlis Walimatulurus',
            'host_name' => 'Abdul Ghafar & Mariam',
            'celebrant_name' => 'Fathan & Ainun',
            'venue' => 'Dewan Seri Indah',
            'message' => 'Semoga majlis ini diberkati.',
        ]);
        Payment::factory()->for($invitation)->create();
        $invitation->refresh();

        $this->assertSame(40, strlen($invitation->public_token));
        $this->actingAs($user)->get(route('payments.show', $invitation))->assertOk()
            ->assertSee('Kongsi melalui WhatsApp')
            ->assertSee($invitation->publicUrl())
            ->assertSee('Salin Pautan')
            ->assertSee('Salin Mesej')
            ->assertSee('https://wa.me/?text=', false)
            ->assertSee('Abdul Ghafar &amp; Mariam', false)
            ->assertSee('Fathan', false)
            ->assertSee('Ainun', false);

        $this->get($invitation->publicUrl())->assertOk()
            ->assertSee('Majlis Walimatulurus')
            ->assertSee('Dewan Seri Indah')
            ->assertSee('Hubungi');
    }

    public function test_public_link_does_not_expose_uncompleted_or_unknown_invitations(): void
    {
        $invitation = Invitation::factory()->create();

        $this->get($invitation->publicUrl())->assertNotFound();
        $this->get(route('invitations.public', str_repeat('a', 40)))->assertNotFound();
    }
}
