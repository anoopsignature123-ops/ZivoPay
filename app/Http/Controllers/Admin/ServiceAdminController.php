<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceAdminController extends Controller
{
    /**
     * Display list of dynamic services and status toggles for Admin.
     */
    public function index(Request $request): View
    {
        $services = AppService::orderBy('sort_order')->get();

        $stats = [
            'total' => $services->count(),
            'active' => $services->where('status', 'active')->where('is_active', true)->count(),
            'coming_soon' => $services->where('status', 'coming_soon')->count(),
            'disabled' => $services->where('is_active', false)->count(),
        ];

        return view('admin.services.index', compact('services', 'stats'));
    }

    /**
     * Toggle service status between active and coming_soon.
     */
    public function toggleStatus(AppService $service): RedirectResponse
    {
        if ($service->status === 'active') {
            $service->status = 'coming_soon';
            $service->is_active = false;
        } else {
            $service->status = 'active';
            $service->is_active = true;
        }

        $service->save();

        return redirect()->back()->with('success', "Service '{$service->title}' status updated to ".str_replace('_', ' ', $service->status).'.');
    }
}
