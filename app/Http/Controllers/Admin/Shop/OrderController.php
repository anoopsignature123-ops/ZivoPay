<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Order;
use App\Services\Shop\ShopOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()->with('user')->withCount('items')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = trim((string) $request->search);
                $query->where(fn ($nested) => $nested->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($users) => $users->where('name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")));
            })
            ->latest()->paginate(10)->withQueryString();

        return view('admin.shop.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        return view('admin.shop.orders.show', ['order' => $order->load(['items', 'user', 'payments'])]);
    }

    public function update(Request $request, Order $order, ShopOrderService $shopOrders): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:confirmed,processing,shipped,out_for_delivery,delivered,cancelled,return_requested,returned'],
            'tracking_number' => ['nullable', 'string', 'max:120'],
            'carrier' => ['nullable', 'string', 'max:120'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $shopOrders->updateStatus($order, $data['status'], $data['tracking_number'] ?? null, $data['carrier'] ?? null, $data['admin_note'] ?? null);

        return redirect()->route('admin.shop.orders.show', $order)->with('success', 'Order updated successfully.');
    }
}
