<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\PromoCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PromoCodeController extends Controller
{
    public function index(Request $request): View
    {
        $promos = PromoCode::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = trim((string) $request->search);
                $query->where(fn ($nested) => $nested->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%"));
            })
            ->when(in_array($request->query('status'), ['active', 'inactive'], true), fn ($query) => $query->where('is_active', $request->query('status') === 'active'))
            ->latest()->paginate(10)->withQueryString();

        return view('admin.shop.promos.index', compact('promos'));
    }

    public function create(): View
    {
        return view('admin.shop.promos.form', ['promo' => new PromoCode]);
    }

    public function store(Request $request): RedirectResponse
    {
        $promo = PromoCode::query()->create($this->validated($request));

        return redirect()->route('admin.shop.promos.index')->with('success', "Promo {$promo->code} created.");
    }

    public function edit(PromoCode $promo): View
    {
        return view('admin.shop.promos.form', compact('promo'));
    }

    public function update(Request $request, PromoCode $promo): RedirectResponse
    {
        $promo->update($this->validated($request, $promo));

        return redirect()->route('admin.shop.promos.index')->with('success', "Promo {$promo->code} updated.");
    }

    public function destroy(PromoCode $promo): RedirectResponse
    {
        $promo->delete();

        return redirect()->route('admin.shop.promos.index')->with('success', 'Promo code deleted.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?PromoCode $promo = null): array
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/', 'unique:shop_promo_codes,code'.($promo ? ','.$promo->id : '')],
            'description' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['required', 'in:fixed,percent'],
            'discount_value' => ['required', 'numeric', 'decimal:0,2', 'gt:0', 'max:1000000'],
            'minimum_subtotal' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'maximum_discount' => ['nullable', 'numeric', 'gt:0', 'max:9999999999'],
            'usage_limit' => ['nullable', 'integer', 'min:1', 'max:10000000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        if ($validated['discount_type'] === 'percent' && (float) $validated['discount_value'] > 100) {
            throw ValidationException::withMessages(['discount_value' => 'Percentage discount cannot exceed 100%.']);
        }

        $validated['code'] = Str::upper($validated['code']);
        $validated['minimum_subtotal'] = $validated['minimum_subtotal'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
