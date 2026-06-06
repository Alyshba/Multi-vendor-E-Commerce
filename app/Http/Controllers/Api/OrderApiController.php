<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderApiController extends Controller
{
    // ─── GET /api/orders ──────────────────────────────────────────────────────

    public function index()
    {
        $vendor = JWTAuth::parseToken()->authenticate();

        $orders = Order::with('product')
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'orders' => $orders,
        ]);
    }

    // ─── POST /api/orders ─────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email',
            'product_id'     => 'required|exists:products,id',
            'quantity'       => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $product = Product::find($request->product_id);

        // Check stock
        if ($product->stock < $request->quantity) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Insufficient stock. Available: ' . $product->stock,
            ], 422);
        }

        $totalPrice = $product->price * $request->quantity;

        $order = Order::create([
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'product_id'     => $request->product_id,
            'vendor_id'      => $product->vendor_id,
            'quantity'       => $request->quantity,
            'total_price'    => $totalPrice,
            'status'         => 'Pending',
        ]);

        // Reduce stock
        $product->decrement('stock', $request->quantity);

        return response()->json([
            'status'  => 'success',
            'message' => 'Order placed successfully',
            'order'   => $order->load('product'),
        ], 201);
    }

    // ─── GET /api/orders/{id} ─────────────────────────────────────────────────

    public function show($id)
    {
        $vendor = JWTAuth::parseToken()->authenticate();

        $order = Order::with('product')
            ->where('vendor_id', $vendor->id)
            ->find($id);

        if (!$order) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'order'  => $order,
        ]);
    }

    // ─── PUT /api/orders/{id}/status ──────────────────────────────────────────

    public function updateStatus(Request $request, $id)
    {
        $vendor = JWTAuth::parseToken()->authenticate();

        $order = Order::where('vendor_id', $vendor->id)->find($id);

        if (!$order) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Order not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Pending,Processing,Shipped,Delivered,Cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Order status updated',
            'order'   => $order,
        ]);
    }
}
