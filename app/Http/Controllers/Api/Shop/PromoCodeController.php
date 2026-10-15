<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Cart;
use App\Models\Shop\PromoCode;
use App\Services\Shop\ShopOrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PromoCodeController extends Controller
{
    use ApiResponse;

    public function check(Request $request, ShopOrderService $orders): JsonResponse
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:50']]);
        $cart = Cart::query()->where('user_id', $request->user()->id)->with('items.product')->first();
        if (! $cart || $cart->items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Add a product to your cart before applying a promo code.']);
        }
        $subtotal = (float) ($cart?->items->sum(fn ($item) => (float) $item->product->price * $item->quantity) ?? 0);
        $promo = PromoCode::query()->whereRaw('UPPER(code) = ?', [Str::upper($data['code'])])->first();
        $discount = $orders->calculatePromoDiscount($promo, $subtotal);
        $delivery = $subtotal >= (float) config('shop.free_delivery_threshold') ? 0 : (float) config('shop.delivery_fee');

        return $this->successResponse([
            'code' => $promo->code,
            'subtotal' => round($subtotal, 2),
            'discount' => $discount,
            'delivery_fee' => $delivery,
            'total' => round(max(0, $subtotal + $delivery - $discount), 2),
        ], 'Promo code applied.');
    }
}
