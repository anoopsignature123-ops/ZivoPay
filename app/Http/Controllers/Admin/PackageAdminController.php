<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageAdminController extends Controller
{
    public function index()
    {
        $packages = Package::orderBy('min_amount', 'asc')->paginate(15);

        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:packages,name',
            'min_amount' => 'required|numeric|min:1',
            'max_amount' => 'required|numeric|gte:min_amount',
            'daily_roi_percentage' => 'required|numeric|min:0.01|max:100',
            'duration_days' => 'required|integer|min:1',
            'direct_bonus_percentage' => 'required|numeric|min:0|max:100',
            'level_income_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        Package::create($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Capital Package created successfully!');
    }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:packages,name,'.$package->id,
            'min_amount' => 'required|numeric|min:1',
            'max_amount' => 'required|numeric|gte:min_amount',
            'daily_roi_percentage' => 'required|numeric|min:0.01|max:100',
            'duration_days' => 'required|integer|min:1',
            'direct_bonus_percentage' => 'required|numeric|min:0|max:100',
            'level_income_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $package->update($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Capital Package updated successfully!');
    }

    public function toggleStatus(Package $package)
    {
        $package->status = $package->status === 'active' ? 'inactive' : 'active';
        $package->save();

        return redirect()->back()
            ->with('success', 'Package status updated to '.strtoupper($package->status));
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Capital Package deleted successfully!');
    }
}
