<?php

namespace App\Http\Controllers;

use App\Models\StaticContent;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display static content page for Mobile App WebViews & Web Portal.
     */
    public function show(?string $slug = 'terms-conditions'): View
    {
        $targetSlug = (string) ($slug ?? 'terms-conditions');

        $normalizedSlug = match (strtolower(trim($targetSlug))) {
            'terms', 'terms-and-conditions' => 'terms-conditions',
            'privacy', 'privacy-and-policy' => 'privacy-policy',
            'contact' => 'contact-us',
            'about' => 'about-us',
            'refund' => 'refund-policy',
            default => strtolower(trim($targetSlug)),
        };

        $page = StaticContent::where('slug', $normalizedSlug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('pages.show', compact('page'));
    }

    /**
     * Alias method for index call.
     */
    public function index(?string $slug = 'terms-conditions'): View
    {
        return $this->show($slug);
    }

    /**
     * Fallback invoke method.
     */
    public function __invoke(?string $slug = 'terms-conditions'): View
    {
        return $this->show($slug);
    }
}
