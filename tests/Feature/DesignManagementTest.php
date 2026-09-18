<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Design;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_only_managers_can_manage_designs(): void
    {
        $design = Design::firstOrFail();
        $this->get(route('manager.designs'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create());
        $this->get(route('manager.designs'))->assertForbidden();
        $this->get(route('manager.designs.create'))->assertForbidden();
        $this->post(route('manager.designs.store'), [])->assertForbidden();
        $this->patch(route('manager.designs.status', $design), ['is_active' => 0])->assertForbidden();
        $this->assertTrue($design->fresh()->is_active);
    }

    public function test_manager_registers_design_and_changes_visibility_without_generating_files(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'manager']));
        $this->get(route('manager.designs'))->assertOk()->assertSee('Rimbun Kasih');
        $this->get(route('manager.designs.create'))->assertOk();
        $this->post(route('manager.designs.store'), ['name' => 'Seri Ujian', 'code' => ' test999 ', 'theme' => 'hari-jadi', 'is_active' => 0])->assertRedirect(route('manager.designs'));
        $design = Design::where('code', 'TEST999')->sole();
        $this->assertFalse($design->is_active);
        $this->get('/design')->assertDontSee('TEST999');
        $this->patch(route('manager.designs.status', $design), ['is_active' => 1])->assertRedirect();
        $this->get('/design?tema=hari-jadi')->assertOk()->assertSee('TEST999')->assertSee('Seri Ujian');
        $this->patch(route('manager.designs.status', $design), ['is_active' => 0])->assertRedirect();
        $this->get('/design')->assertDontSee('TEST999');
        $this->assertFileDoesNotExist(resource_path('views/designs/test999.blade.php'));
        $this->assertFileDoesNotExist(resource_path('js/designs/test999.js'));
        $this->assertFileDoesNotExist(resource_path('css/designs/test999.css'));
        $log = AuditLog::where('action', 'design.deactivated')->sole();
        $this->assertNotNull($log->ip_address);
        $this->assertSame('design', $log->subject_type);
        $this->assertEquals(0, $log->metadata['after']['is_active']);
        $this->get(route('manager.designs', ['status' => 'inactive', 'q' => 'TEST999']))->assertOk()->assertSee('Seri Ujian')->assertDontSee('Rimbun Kasih');
    }

    public function test_invalid_duplicate_and_unsafe_codes_are_rejected(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'manager']));
        $data = ['name' => 'Ujian', 'code' => 'md001', 'theme' => 'perkahwinan', 'is_active' => 0];
        $this->post(route('manager.designs.store'), $data)->assertSessionHasErrors('code');
        $this->post(route('manager.designs.store'), [...$data, 'code' => '../bad'])->assertSessionHasErrors('code');
        $this->post(route('manager.designs.store'), [...$data, 'code' => 'NEW01', 'theme' => 'unknown'])->assertSessionHasErrors('theme');
        $this->patch(route('manager.designs.status', Design::first()), ['is_active' => 'bad'])->assertSessionHasErrors('is_active');
        $this->assertDatabaseCount('designs', 6);
    }

    public function test_inactive_design_cannot_be_selected_but_existing_card_is_preserved(): void
    {
        $design = Design::where('code', 'MD001')->firstOrFail();
        $design->update(['is_active' => false]);
        $card = Invitation::factory()->awaitingPayment()->create(['design_code' => 'MD001', 'design_name' => $design->name, 'status' => 'demo_complete']);
        $this->actingAs($card->user);
        $this->get(route('invitations.options', $card))->assertOk()->assertDontSee('value="MD001"', false);
        $this->put(route('invitations.options.save', $card), ['design_code' => 'MD001'])->assertSessionHasErrors('design_code');
        $this->assertSame('MD001', $card->fresh()->design_code);
        $this->get(route('invitations.public', $card->public_token))->assertOk();
        $this->put(route('invitations.options.save', $card), ['design_code' => 'MD002'])->assertRedirect();
        $this->assertSame('MD002', $card->fresh()->design_code);
    }
}
