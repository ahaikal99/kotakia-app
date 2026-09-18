<?php

namespace Tests\Feature;

use App\Models\Design;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignPreviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_active_template_is_public_and_linked_from_catalog(): void
    {
        $design = Design::factory()->create(['code' => 'KP0001', 'name' => 'Seri', 'is_active' => true]);
        $this->get('/design/kp0001')->assertOk()->assertViewIs('designs.kp0001')->assertSee('Preview Seri');
        $this->get('/design/KP0001')->assertOk();
        $this->get(route('catalog'))->assertSee($design->previewUrl());
        $this->assertContains('resources/js/designs/kp0001.js', $design->assetEntrypoints());
        $this->assertContains('resources/css/designs/kp0001.css', $design->assetEntrypoints());
    }

    public function test_inactive_template_is_only_visible_to_manager(): void
    {
        Design::factory()->create(['code' => 'KP0001', 'is_active' => false]);
        $this->get('/design/kp0001')->assertNotFound();
        $this->actingAs(User::factory()->create())->get('/design/kp0001')->assertNotFound();
        $this->actingAs(User::factory()->create(['role' => 'manager']))->get('/design/kp0001')->assertOk();
    }

    public function test_unknown_missing_and_invalid_templates_are_not_rendered(): void
    {
        $this->get('/design/kp0001')->assertNotFound();
        $design = Design::factory()->create(['code' => 'KP0002', 'is_active' => true]);
        $this->get('/design/kp0002')->assertNotFound();
        $this->assertSame($design->thumbnailUrl(), $design->previewUrl());
        $this->get('/design/layouts.customer')->assertNotFound();
        $this->assertFalse((new Design(['code' => '../layouts/customer']))->hasTemplate());
    }

    public function test_another_design_uses_the_same_controller_without_code_changes(): void
    {
        $design = Design::factory()->create(['code' => 'KP0002', 'is_active' => true]);
        $finder = app('view')->getFinder();
        $finder->addLocation(__DIR__.'/fixtures');
        $this->get('/design/kp0002')->assertOk()->assertViewIs('designs.kp0002')->assertSee($design->name);
    }
}
