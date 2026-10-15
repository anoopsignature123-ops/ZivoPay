<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Banner;
use App\Services\Shop\ShopImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(Request $request): View
    {
        $banners = Banner::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = trim((string) $request->search);
                $query->where(fn ($nested) => $nested->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%"));
            })
            ->when(in_array($request->query('status'), ['active', 'inactive'], true), fn ($query) => $query->where('is_active', $request->query('status') === 'active'))
            ->orderBy('sort_order')->paginate(10)->withQueryString();

        return view('admin.shop.banners.index', compact('banners'));
    }

    public function create(): View
    {
        return view('admin.shop.banners.form', ['banner' => new Banner(['is_active' => true])]);
    }

    public function store(Request $request, ShopImageService $images): RedirectResponse
    {
        $data = $this->validated($request, true);
        $data['image_path'] = $images->upload($request->file('image'), 'shop/banners');
        unset($data['image']);
        $banner = Banner::query()->create($data);

        return redirect()->route('admin.shop.banners.index')->with('success', "Banner {$banner->title} created.");
    }

    public function edit(Banner $banner): View
    {
        return view('admin.shop.banners.form', compact('banner'));
    }

    public function update(Request $request, Banner $banner, ShopImageService $images): RedirectResponse
    {
        $data = $this->validated($request);
        $oldImage = $banner->image_path;
        if ($request->hasFile('image')) {
            $data['image_path'] = $images->upload($request->file('image'), 'shop/banners');
        } else {
            $data['image_path'] = $oldImage;
        }
        unset($data['image']);
        $banner->update($data);
        if ($oldImage !== $banner->image_path) {
            $images->delete($oldImage);
        }

        return redirect()->route('admin.shop.banners.index')->with('success', "Banner {$banner->title} updated.");
    }

    public function destroy(Banner $banner, ShopImageService $images): RedirectResponse
    {
        $image = $banner->image_path;
        $banner->delete();
        $images->delete($image);

        return redirect()->route('admin.shop.banners.index')->with('success', 'Banner deleted.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, bool $requiredImage = false): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'target_url' => ['nullable', 'url', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'image' => [$requiredImage ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]) + ['sort_order' => $request->integer('sort_order'), 'is_active' => $request->boolean('is_active')];
    }
}
