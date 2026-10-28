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

    public function checkout(Request $request, ShopOrderService $shopOrders): JsonResponse
    {
        $data = $request->validate([
            'address_id' => ['required', 'integer', 'exists:shop_addresses,id'],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'customer_note' => ['nullable', 'string', 'max:2000'],
        ]);
        $data['payment_method'] = 'cod';
        $order = $shopOrders->placeOrder($request->user(), $data);
        $payment = $order->payments()->firstOrFail();

        return $this->successResponse([
            'order' => OrderResource::make($order->fresh()->load('items'))->resolve(),
            'payment' => PaymentResource::make($payment->fresh()->load('order'))->resolve(),
        ], 'Shop checkout started.', 201);
    }

    public function paymentHistory(Request $request): JsonResponse
    {
        $payments = Payment::query()->where('user_id', $request->user()->id)->with('order')->latest()->paginate(20);

        return $this->successResponse([
            'items' => PaymentResource::collection($payments->getCollection())->resolve(),
            'pagination' => ['current_page' => $payments->currentPage(), 'last_page' => $payments->lastPage(), 'total' => $payments->total()],
        ], 'Shop payment history retrieved.');
    }

    public function paymentStatus(Request $request): JsonResponse
    {
        $data = $request->validate([
            'payment_id' => ['required', 'integer', 'min:1'],
        ]);
        $payment = Payment::query()
            ->where('user_id', $request->user()->id)
            ->with('order')
            ->find($data['payment_id']);

        if (! $payment) {
            return $this->errorResponse('Shop payment not found. Use the payment_id returned by checkout.', 404);
        }

        return $this->successResponse(PaymentResource::make($payment->load('order'))->resolve(), 'Shop payment status retrieved.');
    }

    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()->where('user_id', $request->user()->id)->with('items')->latest()->paginate(20);

        return $this->successResponse([
            'items' => OrderResource::collection($orders->getCollection())->resolve(),
            'pagination' => ['current_page' => $orders->currentPage(), 'last_page' => $orders->lastPage(), 'total' => $orders->total()],
        ], 'Shop order history retrieved.');
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return $this->successResponse(OrderResource::make($order->load('items'))->resolve(), 'Shop order retrieved.');
    }

    public function cancel(Request $request, ShopOrderService $shopOrders): JsonResponse
    {
        $order = Order::query()->whereKey($request->input('order_id'))->firstOrFail();
        abort_unless($order->user_id === $request->user()->id, 404);
        if (! in_array($order->status, ['pending_payment', 'placed'], true)) {
            throw ValidationException::withMessages(['order' => 'Only newly placed orders can be cancelled in the app. Contact support for help with this order.']);
        }

        $cancelled = $shopOrders->updateStatus($order, 'cancelled', $order->tracking_number, $order->carrier, 'Cancelled by customer.');

        return $this->successResponse(OrderResource::make($cancelled)->resolve(), 'Order cancelled.');
    }

    public function requestReturn(Request $request): JsonResponse
    {
        $order = Order::query()->whereKey($request->input('order_id'))->firstOrFail();
        abort_unless($order->user_id === $request->user()->id, 404);
        $data = $request->validate(['reason' => ['required', 'string', 'max:1000']]);
        $order = DB::transaction(function () use ($request, $order, $data): Order {
            $lockedOrder = Order::query()->where('user_id', $request->user()->id)->lockForUpdate()->findOrFail($order->id);
            if ($lockedOrder->status !== 'delivered' || ! $lockedOrder->delivered_at
                || $lockedOrder->delivered_at->addDays((int) config('shop.return_window_days'))->isPast()) {
                throw ValidationException::withMessages(['order' => 'This order is outside the eligible return period.']);
            }
            $lockedOrder->update([
                'status' => 'return_requested',
                'return_reason' => $data['reason'],
                'return_requested_at' => now(),
            ]);

            return $lockedOrder;
        }, 3);

        return $this->successResponse(OrderResource::make($order->fresh()->load('items'))->resolve(), 'Return request submitted for review.');
    }
}
