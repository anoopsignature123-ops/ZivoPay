<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Address;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        return $this->successResponse(Address::query()->where('user_id', $request->user()->id)->orderByDesc('is_default')->latest()->get(), 'Delivery addresses retrieved.');
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $address = DB::transaction(function () use ($request, $data): Address {
            $userId = $request->user()->id;
            $hasAddresses = Address::query()->where('user_id', $userId)->exists();
            $data['user_id'] = $userId;
            $data['is_default'] = (bool) ($data['is_default'] ?? false) || ! $hasAddresses;
            if ($data['is_default']) {
                Address::query()->where('user_id', $userId)->update(['is_default' => false]);
            }

            return Address::query()->create($data);
        });

        return $this->successResponse($address, 'Delivery address saved.', 201);
    }

    public function update(Request $request): JsonResponse
    {
        $address = Address::query()->findOrFail($request->input('address_id'));
        abort_unless($address->user_id === $request->user()->id, 404);
        $data = $this->validated($request);
        DB::transaction(function () use ($request, $address, $data): void {
            $isDefault = (bool) ($data['is_default'] ?? false);
            if ($isDefault) {
                Address::query()->where('user_id', $request->user()->id)->whereKeyNot($address->id)->update(['is_default' => false]);
            }
            $address->fill($data);
            $address->is_default = $isDefault || $address->is_default;
            $address->save();
        });

        return $this->successResponse($address->fresh(), 'Delivery address updated.');
    }

    public function destroy(Request $request): JsonResponse
    {
        $address = Address::query()->findOrFail($request->input('address_id'));
        abort_unless($address->user_id === $request->user()->id, 404);
        $wasDefault = $address->is_default;
        $userId = $address->user_id;
        $address->delete();
        if ($wasDefault) {
            Address::query()->where('user_id', $userId)->latest('id')->first()?->update(['is_default' => true]);
        }

        return $this->successResponse(null, 'Delivery address deleted.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:40'],
            'recipient_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'state' => ['required', 'string', 'max:120'],
            'postal_code' => ['required', 'string', 'max:12'],
            'country' => ['nullable', 'string', 'max:80'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }
}
