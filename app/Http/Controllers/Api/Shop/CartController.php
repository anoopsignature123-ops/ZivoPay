<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Shop\CartResource;
use App\Models\Shop\Cart;
use App\Models\Shop\CartItem;
use App\Models\Shop\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    use ApiResponse;

    public function show(Request $request): JsonResponse
    {
        return $this->successResponse(CartResource::make($this->cart($request)->load('items.product.category'))->resolve(), 'Shop cart retrieved.');
    }

    public function addItem(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:shop_products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);
        $cart = $this->cart($request);
        DB::transaction(function () use ($cart, $data): void {
            $lockedCart = Cart::query()->lockForUpdate()->findOrFail($cart->id);
            $product = Product::query()->where('is_active', true)->lockForUpdate()->findOrFail($data['product_id']);
            $item = CartItem::query()->where('cart_id', $lockedCart->id)->where('product_id', $product->id)->lockForUpdate()->first();
            $quantity = ($item?->quantity ?? 0) + $data['quantity'];
            if ($product->stock < $quantity) {
                throw ValidationException::withMessages(['quantity' => 'Requested quantity exceeds available stock.']);
            }
            $lockedCart->items()->updateOrCreate(['product_id' => $product->id], ['quantity' => $quantity]);
        }, 3);

        return $this->successResponse(CartResource::make($cart->fresh()->load('items.product.category'))->resolve(), 'Product added to cart.');
    }

    public function updateItem(Request $request): JsonResponse
    {
        $item = CartItem::query()->findOrFail($request->input('item_id'));
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        abort_unless($item->cart()->where('user_id', $request->user()->id)->exists(), 404);
        DB::transaction(function () use ($item, $data): void {
            $product = Product::query()->where('is_active', true)->lockForUpdate()->findOrFail($item->product_id);
            if ($product->stock < $data['quantity']) {
                throw ValidationException::withMessages(['quantity' => 'Requested quantity exceeds available stock.']);
            }
            CartItem::query()->whereKey($item->id)->lockForUpdate()->firstOrFail()->update(['quantity' => $data['quantity']]);
        }, 3);

        return $this->successResponse(CartResource::make($item->cart->fresh()->load('items.product.category'))->resolve(), 'Cart quantity updated.');
    }

    public function removeItem(Request $request): JsonResponse
    {
        $item = CartItem::query()->findOrFail($request->input('item_id'));
        abort_unless($item->cart()->where('user_id', $request->user()->id)->exists(), 404);
        $cart = $item->cart;
        $item->delete();

        return $this->successResponse(CartResource::make($cart->fresh()->load('items.product.category'))->resolve(), 'Product removed from cart.');
    }

    public function clear(Request $request): JsonResponse
    {
        $cart = $this->cart($request);
        $cart->items()->delete();

        return $this->successResponse(CartResource::make($cart->fresh()->load('items.product.category'))->resolve(), 'Cart cleared.');
    }

    private function cart(Request $request): Cart
    {
        return Cart::query()->firstOrCreate(['user_id' => $request->user()->id]);
    }
}
