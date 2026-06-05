<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(): JsonResponse
    {
        $orders = Order::where('user_id', auth('api')->id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string|in:cod,card',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cartItems = Cart::where('user_id', auth('api')->id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        foreach ($cartItems as $item) {
            if (! $item->product) {
                return response()->json([
                    'message' => 'A product in your cart is no longer available',
                ], 400);
            }

            if (! $item->product->is_active || $item->product->approval_status !== 'approved' || $item->product->stock_quantity < $item->quantity) {
                return response()->json([
                    'message' => "Insufficient stock for {$item->product->name}",
                ], 400);
            }
        }

        $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
        $shipping = $subtotal >= 500 ? 0 : 50;
        $total = $subtotal + $shipping;

        $order = DB::transaction(function () use ($request, $cartItems, $subtotal, $shipping, $total) {
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'customer_name' => auth('api')->user()->name,
                'customer_email' => auth('api')->user()->email,
                'user_id' => auth('api')->id(),
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
                OrderItem::create([
                    'order_id' => $order->id,
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

            Cart::where('user_id', auth('api')->id())->delete();

            return $order;
        });

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order->load('items'),
        ], 201);
    }

    public function show(Order $order): JsonResponse
    {
        if ($order->user_id !== auth('api')->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($order->load('items'));
    }

    public function cancel(Order $order): JsonResponse
    {
        if ($order->user_id !== auth('api')->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json(['message' => 'Order cannot be cancelled'], 400);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Order cancelled',
            'order' => $order->fresh()->load('items'),
        ]);
    }
}
