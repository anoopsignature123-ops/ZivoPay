<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\UserPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageController extends Controller
{
    /**
     * Display a listing of all packages & live investment statistics.
     */
    public function index(): View
    {
        $packages = Package::withCount('userPackages')->orderBy('id', 'asc')->get();
        $package = Package::where('status', 'active')->first() ?? Package::first();

        // Platform Summary Statistics KPIs
        $totalInvestments = UserPackage::count();
        $totalCapitalInvested = UserPackage::sum('invested_amount');
        $totalRoiPaid = UserPackage::sum('paid_roi_amount');
        $activePackagesCount = UserPackage::where('status', 'active')->count();

        // Recent Member Investment Packages
        $recentInvestments = UserPackage::with(['user', 'package'])->latest('id')->paginate(10);

        return view('admin.packages.index', compact(
            'packages',
            'package',
            'totalInvestments',
            'totalCapitalInvested',
            'totalRoiPaid',
            'activePackagesCount',
            'recentInvestments'
        ));
    }

    /**
     * Display a comprehensive listing of all user package investments & reports.
     */
    public function history(Request $request): View
    {
        $query = UserPackage::with(['user', 'package']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('referral_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('purchased_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('purchased_at', '<=', $request->end_date);
        }

        $investments = (clone $query)->latest('id')->paginate(15)->withQueryString();

        // Summary Statistics KPIs
        $totalInvestments = UserPackage::count();
        $totalCapitalInvested = UserPackage::sum('invested_amount');
        $totalRoiPaid = UserPackage::sum('paid_roi_amount');
        $activePackagesCount = UserPackage::where('status', 'active')->count();

        $allPackages = Package::orderBy('id', 'asc')->get();

        return view('admin.packages.history', compact(
            'investments',
            'totalInvestments',
            'totalCapitalInvested',
            'totalRoiPaid',
            'activePackagesCount',
            'allPackages'
        ));
    }

    /**
     * Show form to create a new package.
     */
    public function create(): View
    {
        return view('admin.packages.create');
    }

    /**
     * Store a newly created package in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_amount' => 'required|numeric|min:1',
            'max_amount' => 'required|numeric|gte:min_amount',
            'daily_roi' => 'required|numeric|min:0.01|max:100',
            'duration_days' => 'required|integer|min:1',
            'total_return_multiplier' => 'required|numeric|min:1',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        Package::create($validated);

        return redirect()->route('admin.packages.index')->with('success', 'New package created successfully.');
    }

    /**
     * Show form to edit package.
     */
    public function edit(Package $package): View
    {
        return view('admin.packages.edit', compact('package'));
    }

    /**
     * Update specified package in database.
     */
    public function update(Request $request, Package $package): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_amount' => 'required|numeric|min:1',
            'max_amount' => 'required|numeric|gte:min_amount',
            'daily_roi' => 'required|numeric|min:0.01|max:100',
            'duration_days' => 'required|integer|min:1',
            'total_return_multiplier' => 'required|numeric|min:1',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $package->update($validated);

        return redirect()->route('admin.packages.index')->with('success', 'Package updated successfully.');
    }

    /**
     * Quick status toggle for package (active <-> inactive).
     */
    public function toggleStatus(Package $package): RedirectResponse
    {
        $newStatus = $package->status === 'active' ? 'inactive' : 'active';
        $package->update(['status' => $newStatus]);

        return redirect()->back()->with('success', "Package status changed to {$newStatus}.");
    }
}
