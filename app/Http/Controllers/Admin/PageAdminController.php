<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaticContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageAdminController extends Controller
{
    /**
     * Display list of static content pages in Admin Panel.
     */
    public function index(Request $request): View
    {
        $pages = StaticContent::orderBy('id', 'asc')->get();

        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Invoke fallback method for single action or index calls.
     */
    public function __invoke(Request $request): View
    {
        return $this->index($request);
    }

    /**
     * Show edit form for static content page.
     */
    public function edit(StaticContent $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update static content page in database.
     */
    public function update(Request $request, StaticContent $page): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $page->update([
            'title' => trim($request->input('title')),
            'content' => trim($request->input('content')),
            'meta_title' => $request->input('meta_title') ? trim($request->input('meta_title')) : null,
            'meta_description' => $request->input('meta_description') ? trim($request->input('meta_description')) : null,
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
        ]);

        return redirect()->route('admin.pages.index')
            ->with('success', "'{$page->title}' static content updated successfully.");
    }
}
