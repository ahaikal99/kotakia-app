<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_public_pages_have_production_canonicals_and_valid_schema(): void
    {
        config(['seo.google_verification' => 'test-verification']);
        $home = $this->get('/')->assertOk()
            ->assertSee('<link rel="canonical" href="https://ecard.kotakia.my/">', false)
            ->assertSee('name="description"', false)
            ->assertSee('property="og:url"', false)
            ->assertSee('content="test-verification"', false);
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $home->getContent(), $matches);
        $schema = json_decode($matches[1], true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('https://ecard.kotakia.my/', $schema['@graph'][1]['url']);
        $this->get('/design?tema=perkahwinan&susun=nama-az')->assertOk()
            ->assertSee('<link rel="canonical" href="https://ecard.kotakia.my/design">', false);
        $this->get('/design?tema=hari-jadi')->assertOk()->assertSee('content="noindex, follow"', false);
    }

    public function test_sitemap_excludes_private_pages_and_they_have_noindex(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $xml = simplexml_load_string($response->getContent());
        $locations = [];
        foreach ($xml->url as $url) {
            $locations[] = (string) $url->loc;
        }
        $this->assertSame(['https://ecard.kotakia.my/', 'https://ecard.kotakia.my/design'], $locations);
        $this->assertStringContainsString('Sitemap: https://ecard.kotakia.my/sitemap.xml', file_get_contents(public_path('robots.txt')));
        $this->get('/signin')->assertOk()->assertSee('content="noindex, nofollow"', false);
        $invitation = Invitation::factory()->awaitingPayment()->create(['status' => 'demo_complete']);
        $this->get($invitation->publicUrl())->assertOk()->assertSee('content="noindex, nofollow"', false);
        $this->actingAs(User::factory()->manager()->create())->get('/manager')->assertOk()
            ->assertSee('content="noindex, nofollow"', false);
    }
}
