<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Shop\OrderResource;
use App\Http\Resources\Api\Shop\PaymentResource;
use App\Models\Shop\Order;
use App\Services\Shop\ShopOrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    use ApiResponse;

    public function store(Request $request, ShopOrderService $shopOrders): JsonResponse
    {
        $data = $request->validate([
            'address_id' => ['required', 'integer', 'exists:shop_addresses,id'],
            'payment_method' => ['sometimes', 'in:shopping_wallet'],
            'items' => ['sometimes', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required_with:items', 'integer', 'exists:shop_products,id', 'distinct'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1', 'max:99'],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'customer_note' => ['nullable', 'string', 'max:2000'],
        ]);
        $data['payment_method'] ??= 'shopping_wallet';
        $order = $shopOrders->placeOrder($request->user(), $data);
        $payment = $order->payments()->firstOrFail();

        return $this->successResponse([
            'order' => OrderResource::make($order->fresh()->load('items'))->resolve(),
            'payment' => PaymentResource::make($payment->fresh()->load('order'))->resolve(),
            'wallets' => [
                'shopping_wallet' => (float) $request->user()->fresh()->shopping_wallet,
                'currency' => 'INR',
            ],
        ], 'Order placed successfully.', 201);
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:pending_payment,placed,confirmed,processing,shipped,out_for_delivery,delivered,cancelled,return_requested,returned'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $orders = Order::query()->where('user_id', $request->user()->id)
            ->with(['items', 'payments'])
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->latest()->paginate((int) ($filters['per_page'] ?? 20))->withQueryString();

        return $this->successResponse([
            'items' => OrderResource::collection($orders->getCollection())->resolve(),
            'pagination' => ['current_page' => $orders->currentPage(), 'last_page' => $orders->lastPage(), 'per_page' => $orders->perPage(), 'total' => $orders->total()],
        ], 'Shop order history retrieved.');
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return $this->successResponse(OrderResource::make($order->load(['items', 'payments']))->resolve(), 'Shop order retrieved.');
    }

    public function cancel(Request $request, ShopOrderService $shopOrders): JsonResponse
    {
        $data = $request->validate(['order_id' => ['required', 'integer', 'min:1']]);
        $order = Order::query()->whereKey($data['order_id'])->firstOrFail();
        abort_unless($order->user_id === $request->user()->id, 404);
        if (! in_array($order->status, ['pending_payment', 'placed'], true)) {
            throw ValidationException::withMessages(['order' => 'Only newly placed orders can be cancelled in the app. Contact support for help with this order.']);
        }

        $cancelled = $shopOrders->updateStatus($order, 'cancelled', $order->tracking_number, $order->carrier, 'Cancelled by customer.');

        return $this->successResponse(OrderResource::make($cancelled)->resolve(), 'Order cancelled.');
    }

    public function requestReturn(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order_id' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'max:1000'],
            'items' => ['sometimes', 'array', 'min:1', 'max:50'],
            'items.*.order_item_id' => ['required_with:items', 'integer', 'exists:shop_order_items,id', 'distinct'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1', 'max:99'],
        ]);
        $order = Order::query()->whereKey($data['order_id'])->firstOrFail();
        abort_unless($order->user_id === $request->user()->id, 404);
        $order = DB::transaction(function () use ($request, $order, $data): Order {
            $lockedOrder = Order::query()->where('user_id', $request->user()->id)->lockForUpdate()->findOrFail($order->id);
            if ($lockedOrder->status !== 'delivered' || ! $lockedOrder->delivered_at
                || $lockedOrder->delivered_at->addDays((int) config('shop.return_window_days'))->isPast()) {
                throw ValidationException::withMessages(['order' => 'This order is outside the eligible return period.']);
            }
            $orderItems = $lockedOrder->items()->lockForUpdate()->get();
            $returnItems = [];
            $subtotal = (float) $lockedOrder->subtotal;
            $discountRatio = $subtotal > 0 ? max(0, ($subtotal - (float) $lockedOrder->discount) / $subtotal) : 1;
            if (! empty($data['items'])) {
                foreach ($data['items'] as $requestedItem) {
                    $item = $orderItems->firstWhere('id', (int) $requestedItem['order_item_id']);
                    if (! $item || (int) $requestedItem['quantity'] > (int) $item->quantity) {
                        throw ValidationException::withMessages(['items' => 'Return item or quantity does not match this order.']);
                    }
                    $quantity = (int) $requestedItem['quantity'];
                    $returnItems[] = [
                        'order_item_id' => $item->id,
                        'product_id' => $item->product_id,
                        'name' => $item->product_name,
                        'sku' => $item->product_sku,
                        'quantity' => $quantity,
                        'unit_price' => (float) $item->unit_price,
                        'refund_amount' => round((float) $item->unit_price * $quantity * $discountRatio, 2),
                    ];
                }
            } else {
                foreach ($orderItems as $item) {
                    $returnItems[] = [
                        'order_item_id' => $item->id,
                        'product_id' => $item->product_id,
                        'name' => $item->product_name,
                        'sku' => $item->product_sku,
                        'quantity' => (int) $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'refund_amount' => round((float) $item->line_total * $discountRatio, 2),
                    ];
                }
            }
            $lockedOrder->update([
                'status' => 'return_requested',
                'return_reason' => $data['reason'],
                'return_items' => $returnItems,
                'return_requested_at' => now(),
            ]);

            return $lockedOrder;
        }, 3);

        return $this->successResponse(OrderResource::make($order->fresh()->load('items'))->resolve(), 'Return request submitted for review.');
    }
}
