<?php

namespace Tests\Feature;

use App\Models\PromotionBanner;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PromotionBannerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('local');
    }

    private function data(): array
    {
        return ['title' => 'Promosi September', 'expires_on' => now('Asia/Kuala_Lumpur')->addDay()->format('Y-m-d'), 'is_active' => '1', 'poster' => UploadedFile::fake()->image('poster.png', 300, 500)];
    }

    public function test_manager_can_upload_edit_and_deactivate_banner_with_audit(): void
    {
        $manager = User::factory()->manager()->create();
        $this->actingAs($manager)->get('/manager/banners')->assertOk();
        $this->get('/manager/banners/create')->assertOk();
        $this->post('/manager/banners', $this->data())->assertRedirect(route('manager.banners'));
        $banner = PromotionBanner::sole();
        Storage::disk('local')->assertExists($banner->image_path);
        $this->assertSame($manager->id, $banner->created_by);
        $this->assertDatabaseHas('audit_logs', ['action' => 'manager.banner_created', 'user_id' => $manager->id, 'subject_id' => $banner->id]);
        $this->get(route('manager.banners.edit', $banner->id))->assertOk()->assertSee('Promosi September');
        $this->get(route('promotions.poster', $banner->id))->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $old = $banner->image_path;
        $this->put(route('manager.banners.update', $banner->id), [...$this->data(), 'title' => 'Promosi Baharu'])->assertRedirect();
        $banner->refresh();
        Storage::disk('local')->assertMissing($old);
        Storage::disk('local')->assertExists($banner->image_path);
        $this->assertSame('Promosi Baharu', $banner->title);
        $this->patch(route('manager.banners.deactivate', $banner->id))->assertRedirect();
        $this->assertFalse($banner->fresh()->is_active);
        $this->assertDatabaseHas('audit_logs', ['action' => 'manager.banner_deactivated', 'subject_id' => $banner->id]);
    }

    public function test_customers_and_guests_cannot_manage_banners(): void
    {
        $banner = PromotionBanner::factory()->create();
        $this->get('/manager/banners')->assertRedirect(route('login'));
        $this->get(route('promotions.poster', $banner->id))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create());
        foreach (['/manager/banners', '/manager/banners/create', '/manager/banners/'.$banner->id.'/edit'] as $url) {
            $this->get($url)->assertForbidden();
        }
        $this->post('/manager/banners', $this->data())->assertForbidden();
        $this->put(route('manager.banners.update', $banner->id), $this->data())->assertForbidden();
        $this->patch(route('manager.banners.deactivate', $banner->id))->assertForbidden();
    }

    public function test_only_latest_active_unexpired_banner_appears_on_customer_dashboard(): void
    {
        $older = PromotionBanner::factory()->create(['title' => 'Promosi Lama']);
        $latest = PromotionBanner::factory()->create(['title' => 'Promosi Terkini']);
        $expired = PromotionBanner::factory()->create(['title' => 'Sudah Luput', 'expires_at' => now()->subSecond()]);
        $inactive = PromotionBanner::factory()->create(['title' => 'Belum Aktif', 'is_active' => false]);
        $this->actingAs(User::factory()->create())->get('/dashboard')->assertOk()
            ->assertSee('Promosi Terkini')->assertSee('data-promotion-dialog', false)
            ->assertDontSee('Promosi Lama')->assertDontSee('Sudah Luput')->assertDontSee('Belum Aktif');
        $this->get(route('promotions.poster', $expired->id))->assertNotFound();
        $this->get(route('promotions.poster', $inactive->id))->assertNotFound();
        $latest->update(['is_active' => false]);
        $this->get('/dashboard')->assertSee('Promosi Lama')->assertDontSee('Promosi Terkini');
        $older->update(['is_active' => false]);
        $this->get('/dashboard')->assertDontSee('data-promotion-dialog', false);
    }

    public function test_expiry_is_end_of_selected_day_in_malaysia_and_boundary_is_enforced(): void
    {
        $this->travelTo(Carbon::parse('2026-09-13 10:00:00', 'Asia/Kuala_Lumpur')->utc());
        $this->actingAs(User::factory()->manager()->create())->post('/manager/banners', [...$this->data(), 'expires_on' => '2026-09-13'])->assertRedirect();
        $banner = PromotionBanner::sole();
        $this->assertSame('2026-09-13 16:00:00', $banner->expires_at->format('Y-m-d H:i:s'));
        $this->actingAs(User::factory()->create());
        $this->travelTo(Carbon::parse('2026-09-13 15:59:59', 'UTC'));
        $this->get('/dashboard')->assertSee('Promosi September');
        $this->travelTo(Carbon::parse('2026-09-13 16:00:00', 'UTC'));
        $this->get('/dashboard')->assertDontSee('Promosi September');
        $this->get(route('promotions.poster', $banner->id))->assertNotFound();
    }

    public function test_invalid_images_expiry_and_oversized_uploads_are_rejected(): void
    {
        $this->actingAs(User::factory()->manager()->create());
        $this->post('/manager/banners', [...$this->data(), 'poster' => UploadedFile::fake()->create('bad.svg', 10, 'image/svg+xml')])->assertSessionHasErrors('poster');
        $this->post('/manager/banners', [...$this->data(), 'poster' => UploadedFile::fake()->image('big.jpg')->size(2049)])->assertSessionHasErrors('poster');
        $this->post('/manager/banners', [...$this->data(), 'expires_on' => now('Asia/Kuala_Lumpur')->subDay()->format('Y-m-d')])->assertSessionHasErrors('expires_on');
        $this->assertDatabaseCount('promotion_banners', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_edit_without_new_poster_preserves_existing_file(): void
    {
        $this->actingAs(User::factory()->manager()->create())->post('/manager/banners', $this->data())->assertRedirect();
        $banner = PromotionBanner::sole();
        $path = $banner->image_path;
        $this->put(route('manager.banners.update', $banner->id), ['title' => 'Edit Tajuk', 'expires_on' => now('Asia/Kuala_Lumpur')->addDays(5)->format('Y-m-d'), 'is_active' => '1'])->assertRedirect();
        $this->assertSame($path, $banner->fresh()->image_path);
        Storage::disk('local')->assertExists($path);
    }
}
