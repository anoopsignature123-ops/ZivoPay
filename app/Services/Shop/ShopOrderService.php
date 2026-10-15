<?php

namespace App\Services\Shop;

use App\Models\PaymentGatewaySetting;
use App\Models\Shop\Address;
use App\Models\Shop\Cart;
use App\Models\Shop\Order;
use App\Models\Shop\Payment;
use App\Models\Shop\Product;
use App\Models\Shop\PromoCode;
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
            $cart = Cart::query()->where('user_id', $customer->id)->lockForUpdate()->first();
            if (! $cart) {
                throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
            }

            $items = $cart->items()->with('product')->lockForUpdate()->get();
            if ($items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
            }

            $address = Address::query()->where('user_id', $customer->id)->find($data['address_id']);
            if (! $address) {
                throw ValidationException::withMessages(['address_id' => 'Select one of your saved delivery addresses.']);
            }

            $subtotal = 0.0;
            $products = [];
            foreach ($items as $item) {
                $product = Product::query()->lockForUpdate()->find($item->product_id);
                if (! $product || ! $product->is_active || $product->stock < $item->quantity) {
                    throw ValidationException::withMessages(['cart' => 'One or more products are unavailable or have insufficient stock.']);
                }
                $subtotal += (float) $product->price * $item->quantity;
                $products[] = [$item, $product];
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
            $paymentMethod = $data['payment_method'];

            $orderNumber = 'ZIVO-ORD-'.Str::upper(Str::random(10));
            $order = Order::query()->create([
                'user_id' => $customer->id,
                'address_id' => $address->id,
                'order_number' => $orderNumber,
                'status' => $paymentMethod === 'razorpay' ? 'pending_payment' : 'placed',
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
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

            foreach ($products as [$item, $product]) {
                $product->decrement('stock', $item->quantity);
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'product_image' => $product->image_paths[0] ?? null,
                    'unit_price' => $product->price,
                    'quantity' => $item->quantity,
                    'line_total' => round((float) $product->price * $item->quantity, 2),
                ]);
            }

            Payment::query()->create([
                'order_id' => $order->id,
                'user_id' => $customer->id,
                'provider' => $paymentMethod,
                'mode' => $paymentMethod === 'razorpay' ? PaymentGatewaySetting::razorpay()->mode : null,
                'currency' => 'INR',
                'amount' => $total,
                'status' => 'pending',
            ]);

            if ($promo) {
                $promo->increment('usage_count');
            }

            $cart->items()->delete();

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
        if (! in_array($status, $allowedTransitions[$order->status] ?? [], true)) {
            throw ValidationException::withMessages(['status' => 'That order status change is not allowed.']);
        }

        if (in_array($status, ['cancelled', 'returned'], true) && $order->payment_status === 'paid') {
            $payment = $order->payments()->where('status', 'captured')->latest()->first();
            if ($payment?->provider === 'razorpay') {
                app(ShopRazorpayService::class)->refund($payment);
            } elseif ($payment?->provider === 'cod') {
                $payment->update(['status' => 'refund_pending']);
            }
        }

        return DB::transaction(function () use ($order, $status, $trackingNumber, $carrier, $note, $allowedTransitions): Order {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            if (! in_array($status, $allowedTransitions[$lockedOrder->status] ?? [], true)) {
                throw ValidationException::withMessages(['status' => 'That order status change is not allowed.']);
            }

            if (in_array($status, ['cancelled', 'returned'], true)) {
                foreach ($lockedOrder->items as $item) {
                    if ($item->product_id) {
                        Product::query()->whereKey($item->product_id)->lockForUpdate()->increment('stock', $item->quantity);
                    }
                }
                if ($lockedOrder->payment_method === 'cod' && $lockedOrder->payment_status === 'paid') {
                    $lockedOrder->payment_status = 'refund_pending';
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
                if ($lockedOrder->payment_status !== 'paid' && $lockedOrder->payment_status !== 'refunded') {
                    $lockedOrder->payment_status = 'cancelled';
                    $lockedOrder->payments()->whereIn('status', ['pending', 'failed'])->update(['status' => 'cancelled']);
                }
            }
            $lockedOrder->save();

            return $lockedOrder->load(['items', 'user']);
        }, 3);
    }
}
