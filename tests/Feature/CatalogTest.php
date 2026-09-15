<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_displays_existing_designs_and_homepage_links_to_it(): void
    {
        $this->withoutVite();
        $this->get('/')->assertOk()->assertSee(route('catalog'));
        $response = $this->get('/design')->assertOk();

        foreach (config('catalog.designs') as $design) {
            $response->assertSee($design['code'])->assertSee($design['name']);
            $this->assertFileExists(public_path('image/KATALOG/'.$design['code'].'.png'));
        }
    }

    public function test_theme_filters_results_and_empty_themes_have_a_reset_link(): void
    {
        $this->withoutVite();
        $this->get('/design?tema=perkahwinan')->assertOk()->assertSee('MD001');

        foreach (['hari-jadi', 'jamuan', 'lain-lain'] as $theme) {
            $this->get('/design?tema='.$theme)->assertOk()
                ->assertDontSee('MD001')
                ->assertSee('Koleksi ini belum tersedia')
                ->assertSee('Lihat Semua Reka Bentuk');
        }
    }

    public function test_sorting_and_invalid_filters_are_handled(): void
    {
        $this->withoutVite();
        $this->get('/design?tema=perkahwinan&susun=nama-az')->assertOk()
            ->assertSeeInOrder(['Elegan Malam', 'Mawar Romantis', 'Neon Cinta', 'Pesona Emas', 'Rimbun Kasih', 'Sutera Senja']);
        $this->get('/design?susun=nama-za')->assertOk()
            ->assertSeeInOrder(['Sutera Senja', 'Rimbun Kasih', 'Pesona Emas', 'Neon Cinta', 'Mawar Romantis', 'Elegan Malam']);

        foreach (['tema=unknown&susun=unknown', 'tema[]=invalid&susun[]=invalid'] as $query) {
            $this->get('/design?'.$query)->assertOk()
                ->assertSeeInOrder(['MD001', 'MD002', 'MD003', 'MD004', 'MD005', 'MD006']);
        }
    }
}
