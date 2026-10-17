<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Rating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RatingController extends Controller
{
    public function index(Request $request): View
    {
        $ratings = Rating::query()->with(['product:id,name,slug', 'user:id,name,email,mobile'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = trim((string) $request->query('search'));
                $query->where(function ($nested) use ($search): void {
                    $nested->whereHas('product', fn ($product) => $product->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->latest()->paginate(10)->withQueryString();

        return view('admin.shop.ratings.index', compact('ratings'));
    }

    public function edit(Rating $rating): View
    {
        return view('admin.shop.ratings.edit', compact('rating'));
    }

    public function update(Request $request, Rating $rating): RedirectResponse
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'review' => ['nullable', 'string', 'max:2000'],
        ]);
        $rating->update($data);

        return redirect()->route('admin.shop.ratings.index')->with('success', 'Product rating updated.');
    }

    public function destroy(Rating $rating): RedirectResponse
    {
        $rating->delete();

        return redirect()->route('admin.shop.ratings.index')->with('success', 'Product rating deleted.');
    }
}
