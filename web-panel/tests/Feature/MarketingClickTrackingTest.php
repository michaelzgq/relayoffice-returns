<?php

namespace Tests\Feature;

use App\Models\MarketingClickEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsReturnsFixtures;
use Tests\TestCase;

class MarketingClickTrackingTest extends TestCase
{
    use BuildsReturnsFixtures;
    use RefreshDatabase;

    public function test_public_marketing_click_endpoint_stores_supported_click_event(): void
    {
        $response = $this->post('http://dossentry.com/marketing/click-events', [
            'page_key' => 'landing',
            'placement' => 'hero',
            'cta_key' => 'sample_review',
            'cta_label' => 'View Sample Brand Review Link',
            'source_url' => 'http://dossentry.com/?utm_source=linkedin&utm_medium=dm',
            'landing_url' => 'http://dossentry.com/?utm_source=linkedin&utm_medium=dm',
            'target_url' => 'https://dossentry.com/brand-review/3',
            'client_token' => 'test-client-1',
            'utm_source' => 'linkedin',
            'utm_medium' => 'dm',
        ]);

        $response->assertNoContent();

        $this->assertDatabaseHas('marketing_click_events', [
            'page_key' => 'landing',
            'placement' => 'hero',
            'cta_key' => 'sample_review',
            'source_host' => 'dossentry.com',
            'source_path' => '/',
            'landing_path' => '/',
            'target_host' => 'dossentry.com',
            'target_path' => '/brand-review/3',
            'client_token' => 'test-client-1',
            'utm_source' => 'linkedin',
            'utm_medium' => 'dm',
        ]);
    }

    public function test_marketing_click_endpoint_ignores_mismatched_source_host(): void
    {
        $response = $this->post('http://dossentry.com/marketing/click-events', [
            'page_key' => 'landing',
            'placement' => 'hero',
            'cta_key' => 'sample_review',
            'cta_label' => 'View Sample Brand Review Link',
            'source_url' => 'https://example.com/',
            'landing_url' => 'https://example.com/',
            'target_url' => 'https://dossentry.com/brand-review/3',
            'client_token' => 'test-client-2',
        ]);

        $response->assertNoContent();
        $this->assertDatabaseCount('marketing_click_events', 0);
    }

    public function test_admin_review_requests_page_shows_click_tracking_summary(): void
    {
        $admin = $this->signInAdmin();

        MarketingClickEvent::query()->create([
            'page_key' => 'landing',
            'placement' => 'hero',
            'cta_key' => 'sample_review',
            'cta_label' => 'View Sample Brand Review Link',
            'source_host' => 'dossentry.com',
            'source_path' => '/',
            'landing_path' => '/',
            'target_host' => 'dossentry.com',
            'target_path' => '/brand-review/3',
            'client_token' => 'tracked-client-1',
            'utm_source' => 'linkedin',
            'created_at' => now()->subHour(),
            'updated_at' => now()->subHour(),
        ]);

        MarketingClickEvent::query()->create([
            'page_key' => 'compare',
            'placement' => 'cta',
            'cta_key' => 'workflow_review',
            'cta_label' => 'Request Workflow Review',
            'source_host' => 'dossentry.com',
            'source_path' => '/compare/generic-inspection-apps',
            'landing_path' => '/?utm_source=linkedin',
            'target_host' => 'dossentry.com',
            'target_path' => '/#review-request',
            'client_token' => 'tracked-client-1',
            'utm_source' => 'linkedin',
            'created_at' => now()->subMinutes(20),
            'updated_at' => now()->subMinutes(20),
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.returns.review-requests.index'));

        $response->assertOk();
        $response->assertSee('CTA Click Tracking');
        $response->assertSee('Recent CTA clicks');
        $response->assertSee('Sample review', false);
        $response->assertSee('Request Workflow Review');
        $response->assertSee('src=linkedin', false);
    }
}
