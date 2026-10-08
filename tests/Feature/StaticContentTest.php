<?php

namespace Tests\Feature;

use App\Models\StaticContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaticContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        StaticContent::updateOrCreate(
            ['slug' => 'terms-conditions'],
            [
                'title' => 'Terms & Conditions',
                'category' => 'legal',
                'content' => '<h2>Terms & Conditions</h2><p>ZIVO PAY Terms of use.</p>',
                'meta_title' => 'Terms - ZIVO PAY',
                'meta_description' => 'Terms and conditions documentation.',
                'is_active' => true,
            ]
        );

        StaticContent::updateOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Privacy Policy',
                'category' => 'legal',
                'content' => '<h2>Privacy Policy</h2><p>ZIVO PAY Privacy policy.</p>',
                'meta_title' => 'Privacy - ZIVO PAY',
                'meta_description' => 'Privacy policy documentation.',
                'is_active' => true,
            ]
        );
    }

    /**
     * Test public WebView rendering for mobile apps and web portal.
     */
    public function test_public_webview_renders_static_pages(): void
    {
        // 1. Direct slug webview route
        $resSlug = $this->get('/page/terms-conditions');
        $resSlug->assertStatus(200)
            ->assertSee('Terms & Conditions')
            ->assertSee('ZIVO PAY Terms of use.');

        // 2. Shortcut route for terms
        $resTerms = $this->get('/terms');
        $resTerms->assertStatus(200)
            ->assertSee('Terms & Conditions');

        // 3. Shortcut route for privacy policy
        $resPrivacy = $this->get('/privacy-policy');
        $resPrivacy->assertStatus(200)
            ->assertSee('Privacy Policy');
    }

    /**
     * Test REST API endpoints for fetching static page content.
     */
    public function test_rest_api_returns_static_pages_json(): void
    {
        // 1. List all active static pages
        $listRes = $this->getJson('/api/pages');
        $listRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data.pages');

        // 2. Fetch single static page content JSON
        $singleRes = $this->getJson('/api/pages/terms-conditions');
        $singleRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.page.slug', 'terms-conditions')
            ->assertJsonPath('data.page.title', 'Terms & Conditions');
    }

    /**
     * Test Admin panel viewing and editing static content page.
     */
    public function test_admin_can_view_and_edit_static_content(): void
    {
        $admin = User::factory()->create([
            'role_id' => 1,
        ]);
        $this->actingAs($admin);

        $page = StaticContent::where('slug', 'terms-conditions')->first();

        // Admin index
        $indexRes = $this->get(route('admin.pages.index'));
        $indexRes->assertStatus(200)
            ->assertSee('Terms & Conditions');

        // Admin edit form
        $editRes = $this->get(route('admin.pages.edit', $page));
        $editRes->assertStatus(200);

        // Admin update submission
        $updateRes = $this->put(route('admin.pages.update', $page), [
            'title' => 'Terms & Conditions Updated',
            'content' => '<h2>Updated Terms</h2><p>New updated terms text.</p>',
            'meta_title' => 'Updated Terms - ZIVO PAY',
            'meta_description' => 'Updated description.',
            'is_active' => true,
        ]);

        $updateRes->assertRedirect(route('admin.pages.index'));

        $this->assertDatabaseHas('static_contents', [
            'id' => $page->id,
            'title' => 'Terms & Conditions Updated',
        ]);
    }
}
