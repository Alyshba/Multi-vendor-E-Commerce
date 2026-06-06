<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('customer.orders.show', [
            'order' => $order->load('items.product.vendor'),
        ]);
    }

    public function checkout(): View
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart')->with('error', 'Your cart is empty');
        }

        $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
        $shipping = $subtotal >= 500 ? 0 : 50;
        $total = $subtotal + $shipping;

        return view('customer.checkout.index', compact('cartItems', 'subtotal', 'shipping', 'total'));
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string|in:cod,card',
            'notes' => 'nullable|string',
        ]);

        $cartItems = Cart::where('user_id', Auth::id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Cart is empty');
        }

        foreach ($cartItems as $item) {
            if (! $item->product) {
                return back()->with('error', 'A product in your cart is no longer available');
            }

            if (! $item->product->is_active || $item->product->approval_status !== 'approved' || $item->product->stock_quantity < $item->quantity) {
                return back()->with('error', "Insufficient stock for {$item->product->name}");
            }
        }

        $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
        $shipping = $subtotal >= 500 ? 0 : 50;
        $total = $subtotal + $shipping;

        DB::transaction(function () use ($request, $cartItems, $subtotal, $shipping, $total) {
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'customer_name' => Auth::user()->name,
                'customer_email' => Auth::user()->email,
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $total,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid',
                'shipping_address' => $request->shipping_address,
                'notes' => $request->notes,
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'unit_price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'total' => $item->product->price * $item->quantity,
                    'line_total' => $item->product->price * $item->quantity,
                ]);

                $item->product->decrement('stock', $item->quantity);
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            Cart::where('user_id', Auth::id())->delete();
        });

        return redirect()->route('customer.orders')->with('success', 'Order placed successfully');
    }

    public function cancel(Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->with('error', 'Order cannot be cancelled');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order cancelled');
    }
}
