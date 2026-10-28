<?php

namespace App\Services\Shop;

use App\Models\Shop\Address;
use App\Models\Shop\Cart;
use App\Models\Shop\Order;
use App\Models\Shop\Payment;
use App\Models\Shop\Product;
use App\Models\Shop\PromoCode;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ShopOrderService
{
    /** @param array<string, mixed> $data */
    public function placeOrder(User $customer, array $data): Order
    {
        return DB::transaction(function () use ($customer, $data): Order {
            $customer = User::query()->lockForUpdate()->findOrFail($customer->id);
            $cart = null;
            $lineItems = [];
            if (! empty($data['items'])) {
                $requestedItems = collect($data['items'])->keyBy(fn (array $item): int => (int) $item['product_id']);
                $products = Product::query()->whereIn('id', $requestedItems->keys())->orderBy('id')->lockForUpdate()->get()->keyBy('id');
                if ($products->count() !== $requestedItems->count()) {
                    throw ValidationException::withMessages(['items' => 'One or more products were not found.']);
                }
                foreach ($requestedItems as $productId => $requestedItem) {
                    $lineItems[] = [
                        'product' => $products->get($productId),
                        'quantity' => (int) $requestedItem['quantity'],
                    ];
                }
            } else {
                $cart = Cart::query()->where('user_id', $customer->id)->lockForUpdate()->first();
                if (! $cart) {
                    throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
                }
                $cartItems = $cart->items()->orderBy('product_id')->lockForUpdate()->get();
                foreach ($cartItems as $cartItem) {
                    $product = Product::query()->lockForUpdate()->find($cartItem->product_id);
                    $lineItems[] = ['product' => $product, 'quantity' => (int) $cartItem->quantity];
                }
            }
            if ($lineItems === []) {
                throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
            }

            $address = Address::query()->where('user_id', $customer->id)->find($data['address_id']);
            if (! $address) {
                throw ValidationException::withMessages(['address_id' => 'Select one of your saved delivery addresses.']);
            }

            $subtotal = 0.0;
            foreach ($lineItems as $lineItem) {
                $product = $lineItem['product'];
                $quantity = $lineItem['quantity'];
                if (! $product || ! $product->is_active || $product->stock < $quantity) {
                    throw ValidationException::withMessages(['cart' => 'One or more products are unavailable or have insufficient stock.']);
                }
                $subtotal += (float) $product->price * $quantity;
            }
            $subtotal = round($subtotal, 2);

            $promo = null;
            $discount = 0.0;
            if (! empty($data['promo_code'])) {
                $promo = PromoCode::query()->whereRaw('UPPER(code) = ?', [Str::upper($data['promo_code'])])->lockForUpdate()->first();
                $discount = $this->calculatePromoDiscount($promo, $subtotal);
            }

            $deliveryFee = $subtotal >= (float) config('shop.free_delivery_threshold')
                ? 0.0
                : (float) config('shop.delivery_fee');
            $total = round(max(0, $subtotal + $deliveryFee - $discount), 2);
            $paymentMethod = $data['payment_method'] ?? 'cod';
            if ($paymentMethod === 'fund_wallet' && (float) $customer->deposit_wallet < $total) {
                throw ValidationException::withMessages([
                    'payment_method' => 'Insufficient Fund Wallet balance. Available ₹'.number_format((float) $customer->deposit_wallet, 2).'; required ₹'.number_format($total, 2).'.',
                ]);
            }

            $orderNumber = 'ZIVO-ORD-'.Str::upper(Str::random(10));
            $order = Order::query()->create([
                'user_id' => $customer->id,
                'address_id' => $address->id,
                'order_number' => $orderNumber,
                'status' => 'placed',
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'fund_wallet' ? 'paid' : 'pending',
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount' => $discount,
                'total' => $total,
                'promo_code' => $promo?->code,
                'recipient_name' => $address->recipient_name,
                'phone' => $address->phone,
                'address_line1' => $address->address_line1,
                'address_line2' => $address->address_line2,
                'landmark' => $address->landmark,
                'city' => $address->city,
                'state' => $address->state,
                'postal_code' => $address->postal_code,
                'country' => $address->country,
                'customer_note' => $data['customer_note'] ?? null,
            ]);

            foreach ($lineItems as $lineItem) {
                $product = $lineItem['product'];
                $quantity = $lineItem['quantity'];
                $product->decrement('stock', $quantity);
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'product_image' => $product->image_paths[0] ?? null,
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                    'line_total' => round((float) $product->price * $quantity, 2),
                ]);
            }

            if ($paymentMethod === 'fund_wallet') {
                $customer->deposit_wallet = round((float) $customer->deposit_wallet - $total, 2);
                $customer->save();
                Transaction::query()->create([
                    'user_id' => $customer->id,
                    'amount' => $total,
                    'wallet_type' => 'deposit_wallet',
                    'type' => 'shop_order',
                    'trx_type' => '-',
                    'charge' => 0,
                    'post_balance' => $customer->deposit_wallet,
                    'description' => "Shop order payment ({$orderNumber})",
                    'trx_id' => 'SHOP-'.$orderNumber,
                ]);
            }

            Payment::query()->create([
                'order_id' => $order->id,
                'user_id' => $customer->id,
                'provider' => $paymentMethod,
                'mode' => null,
                'currency' => 'INR',
                'amount' => $total,
                'status' => $paymentMethod === 'fund_wallet' ? 'captured' : 'pending',
                'method' => $paymentMethod,
                'paid_at' => $paymentMethod === 'fund_wallet' ? now() : null,
            ]);

            if ($promo) {
                $promo->increment('usage_count');
            }

            if ($cart) {
                $cart->items()->delete();
            }

            return $order->load('items');
        }, 3);
    }

    public function calculatePromoDiscount(?PromoCode $promo, float $subtotal): float
    {
        if (! $promo || ! $promo->is_active || ($promo->starts_at && $promo->starts_at->isFuture())
            || ($promo->ends_at && $promo->ends_at->isPast())
            || ($promo->usage_limit !== null && $promo->usage_count >= $promo->usage_limit)
            || $subtotal < (float) $promo->minimum_subtotal) {
            throw ValidationException::withMessages(['promo_code' => 'This promo code is invalid, expired, or does not meet the minimum order amount.']);
        }

        $discount = $promo->discount_type === 'percent'
            ? $subtotal * (float) $promo->discount_value / 100
            : (float) $promo->discount_value;
        if ($promo->maximum_discount !== null) {
            $discount = min($discount, (float) $promo->maximum_discount);
        }

        return round(min($subtotal, $discount), 2);
    }

    public function updateStatus(Order $order, string $status, ?string $trackingNumber = null, ?string $carrier = null, ?string $note = null): Order
    {
        $allowedTransitions = [
            'pending_payment' => ['cancelled'],
            'placed' => ['confirmed', 'cancelled'],
            'confirmed' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['out_for_delivery'],
            'out_for_delivery' => ['delivered'],
            'delivered' => ['return_requested'],
            'return_requested' => ['returned', 'delivered'],
        ];

        return DB::transaction(function () use ($order, $status, $trackingNumber, $carrier, $note, $allowedTransitions): Order {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            if (! in_array($status, $allowedTransitions[$lockedOrder->status] ?? [], true)) {
                throw ValidationException::withMessages(['status' => 'That order status change is not allowed.']);
            }

            $lockedOrder->loadMissing(['items', 'payments']);
            if (in_array($status, ['cancelled', 'returned'], true)) {
                $this->restoreStock($lockedOrder, $status);
                if ($lockedOrder->payment_status === 'paid') {
                    $this->refundToFundWallet($lockedOrder, $status === 'returned' ? $this->returnRefundAmount($lockedOrder) : (float) $lockedOrder->total);
                } else {
                    $lockedOrder->payment_status = 'cancelled';
                    $lockedOrder->payments()->whereIn('status', ['pending', 'failed'])->update(['status' => 'cancelled']);
                }
            }

            $lockedOrder->status = $status;
            $lockedOrder->tracking_number = $trackingNumber;
            $lockedOrder->carrier = $carrier;
            $lockedOrder->admin_note = $note;
            if ($status === 'delivered') {
                $lockedOrder->delivered_at = now();
                if ($lockedOrder->payment_method === 'cod' && $lockedOrder->payment_status !== 'paid') {
                    $lockedOrder->payment_status = 'paid';
                    $lockedOrder->payments()->where('provider', 'cod')->where('status', 'pending')->update([
                        'status' => 'captured', 'paid_at' => now(),
                    ]);
                }
            }
            if ($status === 'cancelled') {
                $lockedOrder->cancelled_at = now();
            }
            $lockedOrder->save();

            return $lockedOrder->load(['items', 'user', 'payments']);
        }, 3);
    }

    private function restoreStock(Order $order, string $status): void
    {
        $quantities = $status === 'returned' && is_array($order->return_items)
            ? collect($order->return_items)->keyBy('order_item_id')->map(fn (array $item): int => (int) $item['quantity'])
            : $order->items->keyBy('id')->map(fn ($item): int => (int) $item->quantity);

        foreach ($order->items as $item) {
            $quantity = (int) ($quantities->get($item->id) ?? 0);
            if ($item->product_id && $quantity > 0) {
                Product::query()->whereKey($item->product_id)->lockForUpdate()->increment('stock', $quantity);
            }
        }
    }

    private function returnRefundAmount(Order $order): float
    {
        if (! is_array($order->return_items) || $order->return_items === []) {
            return (float) $order->total;
        }

        $refundAmount = array_sum(array_column($order->return_items, 'refund_amount'));
        $returnedQuantities = collect($order->return_items)->keyBy('order_item_id');
        $isFullReturn = $order->items->every(fn ($item): bool => (int) data_get($returnedQuantities->get($item->id), 'quantity', 0) >= (int) $item->quantity);
        if ($isFullReturn) {
            return round(max(0, (float) $order->total - (float) $order->refunded_amount), 2);
        }

        return round(min((float) $order->total - (float) $order->refunded_amount, $refundAmount), 2);
    }

    private function refundToFundWallet(Order $order, float $amount): void
    {
        if ($amount <= 0 || in_array($order->payment_status, ['refunded', 'partially_refunded'], true)) {
            return;
        }

        $user = User::query()->lockForUpdate()->findOrFail($order->user_id);
        $user->deposit_wallet = round((float) $user->deposit_wallet + $amount, 2);
        $user->save();

        Transaction::query()->create([
            'user_id' => $user->id,
            'amount' => $amount,
            'wallet_type' => 'deposit_wallet',
            'type' => 'shop_order_refund',
            'trx_type' => '+',
            'charge' => 0,
            'post_balance' => $user->deposit_wallet,
            'description' => "Fund Wallet refund for shop order ({$order->order_number})",
            'trx_id' => 'SHOP-REF-'.$order->order_number,
        ]);

        $order->refunded_amount = round((float) $order->refunded_amount + $amount, 2);
        $order->payment_status = $order->refunded_amount >= (float) $order->total ? 'refunded' : 'partially_refunded';
        $order->payments()->whereIn('status', ['captured', 'refund_pending'])->latest()->limit(1)->get()->each(function (Payment $payment) use ($amount, $order): void {
            $metadata = $payment->metadata ?? [];
            $metadata['refund_amount'] = $amount;
            $payment->update([
                'status' => $order->payment_status,
                'metadata' => $metadata,
                'refunded_at' => now(),
            ]);
        });
    }
}
