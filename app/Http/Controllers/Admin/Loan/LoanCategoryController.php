<?php

namespace App\Http\Controllers\Admin\Loan;

use App\Http\Controllers\Controller;
use App\Models\LoanCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LoanCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:120'], 'status' => ['nullable', Rule::in(['active', 'inactive'])]]);
        $categories = LoanCategory::query()->withCount('applications')
            ->when(filled($filters['search'] ?? null), fn ($query) => $query->where(fn ($nested) => $nested->where('name', 'like', '%'.$filters['search'].'%')->orWhere('slug', 'like', '%'.$filters['search'].'%')))
            ->when(isset($filters['status']), fn ($query) => $query->where('is_active', $filters['status'] === 'active'))
            ->orderBy('sort_order')->orderBy('name')->paginate(15)->withQueryString();

        $categoryCounts = LoanCategory::query()
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active, SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive')
            ->first();

        return view('admin.loan.categories.index', [
            'categories' => $categories,
            'filters' => $filters,
            'stats' => [
                'total' => (int) ($categoryCounts->total ?? 0),
                'active' => (int) ($categoryCounts->active ?? 0),
                'inactive' => (int) ($categoryCounts->inactive ?? 0),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.loan.categories.form', ['category' => new LoanCategory]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['name']);
        LoanCategory::query()->create($data);

        return redirect()->route('admin.loan.categories.index')->with('success', 'Loan category created successfully.');
    }

    public function edit(LoanCategory $category): View
    {
        return view('admin.loan.categories.form', compact('category'));
    }

    public function update(Request $request, LoanCategory $category): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['name'], $category->id);
        $category->update($data);

        return redirect()->route('admin.loan.categories.index')->with('success', 'Loan category updated successfully.');
    }

    public function destroy(LoanCategory $category): RedirectResponse
    {
        if ($category->applications()->exists()) {
            return back()->with('error', 'This category has applications and cannot be deleted. Deactivate it instead.');
        }
        $category->delete();

        return back()->with('success', 'Loan category deleted.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'], 'slug' => ['nullable', 'string', 'max:140'],
            'description' => ['nullable', 'string', 'max:2000'], 'icon' => ['nullable', 'string', 'max:100'],
            'indicative_interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'minimum_amount' => ['nullable', 'numeric', 'min:0'], 'maximum_amount' => ['nullable', 'numeric', 'gt:minimum_amount'],
            'maximum_tenure_years' => ['nullable', 'integer', 'min:1', 'max:50'],
            'sort_order' => ['required', 'integer', 'min:0'], 'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $slug = Str::slug($value) ?: 'loan-category';
        $candidate = $slug;
        $suffix = 2;
        while (LoanCategory::query()->where('slug', $candidate)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $candidate = $slug.'-'.$suffix++;
        }

        return $candidate;
    }
}
