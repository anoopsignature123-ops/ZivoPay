<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StaticContent;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class PageApiController extends Controller
{
    use ApiResponse;

    /**
     * Get list of all available static pages & webview URLs API.
     */
    public function index(): JsonResponse
    {
        $pages = StaticContent::where('is_active', true)
            ->get(['id', 'slug', 'title', 'category', 'updated_at'])
            ->map(function ($page) {
                return [
                    'id' => $page->id,
                    'slug' => $page->slug,
                    'title' => $page->title,
                    'category' => $page->category,
                    'webview_url' => url('/page/'.$page->slug),
                    'updated_at' => $page->updated_at->toIso8601String(),
                ];
            });

        return $this->successResponse([
            'pages' => $pages,
        ], 'Static content pages retrieved successfully.');
    }

    /**
     * Get static page content JSON API by slug.
     */
    public function show(?string $slug = 'terms-conditions'): JsonResponse
    {
        $targetSlug = (string) ($slug ?? 'terms-conditions');

        $normalizedSlug = match (strtolower(trim($targetSlug))) {
            'terms', 'terms-and-conditions' => 'terms-conditions',
            'privacy', 'privacy-and-policy' => 'privacy-policy',
            'contact' => 'contact-us',
            'about' => 'about-us',
            'refund' => 'refund-policy',
            default => strtolower(trim($slug)),
        };

        $page = StaticContent::where('slug', $normalizedSlug)
            ->where('is_active', true)
            ->first();

        if (! $page) {
            return $this->errorResponse('Static page content not found.', 404);
        }

        return $this->successResponse([
            'page' => [
                'id' => $page->id,
                'slug' => $page->slug,
                'title' => $page->title,
                'category' => $page->category,
                'content' => $page->content,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
                'webview_url' => url('/page/'.$page->slug),
                'updated_at' => $page->updated_at->toIso8601String(),
                'updated_at_formatted' => $page->updated_at->format('d M Y, h:i A'),
            ],
        ], 'Static page content retrieved successfully.');
    }
}
