<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderApiController extends Controller
{
    protected function getVendorId()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return 0;
        }
        $vendor = Vendor::where('user_id', $user->id)->first() ?? Vendor::where('email', $user->email)->first();
        return $vendor ? $vendor->id : 0;
    }

    public function index()
    {
        $vendorId = $this->getVendorId();

        $items = OrderItem::with(['order', 'product'])
            ->whereHas('product', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->latest()
            ->get();

        $orders = $items->map(function ($item) {
            return [
                'id'             => $item->id,
                'customer_name'  => $item->order->customer_name ?? 'N/A',
                'customer_email' => $item->order->customer_email ?? 'N/A',
                'product_id'     => $item->product_id,
                'vendor_id'      => $item->product->vendor_id ?? 0,
                'quantity'       => $item->quantity,
                'total_price'    => $item->line_total,
                'status'         => ucfirst($item->order->status ?? 'pending'),
                'created_at'     => $item->created_at,
                'updated_at'     => $item->updated_at,
                'product'        => $item->product,
            ];
        });

        return response()->json([
            'status' => 'success',
            'orders' => $orders,
        ]);
    }

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
        if ($product->stock_quantity < $request->quantity) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Insufficient stock. Available: ' . $product->stock_quantity,
            ], 422);
        }

        $totalPrice = $product->price * $request->quantity;

        // Create Order record
        $order = Order::create([
            'order_number'   => 'ORD-' . strtoupper(uniqid()),
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'status'         => 'pending',
            'total_amount'   => $totalPrice,
            'subtotal'       => $totalPrice,
            'total'          => $totalPrice,
            'payment_status' => 'unpaid',
        ]);

        // Create OrderItem record
        $item = $order->items()->create([
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'price'        => $product->price,
            'quantity'     => $request->quantity,
            'total'        => $totalPrice,
            'unit_price'   => $product->price,
            'line_total'   => $totalPrice,
        ]);

        // Reduce stock
        $product->decrement('stock', $request->quantity);
        $product->decrement('stock_quantity', $request->quantity);

        $mappedOrder = [
            'id'             => $item->id,
            'customer_name'  => $order->customer_name,
            'customer_email' => $order->customer_email,
            'product_id'     => $item->product_id,
            'vendor_id'      => $product->vendor_id,
            'quantity'       => $item->quantity,
            'total_price'    => $item->line_total,
            'status'         => ucfirst($order->status),
            'created_at'     => $item->created_at,
            'updated_at'     => $item->updated_at,
            'product'        => $product,
        ];

        return response()->json([
            'status'  => 'success',
            'message' => 'Order placed successfully',
            'order'   => $mappedOrder,
        ], 201);
    }

    public function show($id)
    {
        $vendorId = $this->getVendorId();

        $item = OrderItem::with(['order', 'product'])
            ->whereHas('product', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->find($id);

        if (!$item) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Order not found',
            ], 404);
        }

        $mappedOrder = [
            'id'             => $item->id,
            'customer_name'  => $item->order->customer_name ?? 'N/A',
            'customer_email' => $item->order->customer_email ?? 'N/A',
            'product_id'     => $item->product_id,
            'vendor_id'      => $item->product->vendor_id ?? 0,
            'quantity'       => $item->quantity,
            'total_price'    => $item->line_total,
            'status'         => ucfirst($item->order->status ?? 'pending'),
            'created_at'     => $item->created_at,
            'updated_at'     => $item->updated_at,
            'product'        => $item->product,
        ];

        return response()->json([
            'status' => 'success',
            'order'  => $mappedOrder,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $vendorId = $this->getVendorId();

        $item = OrderItem::with(['order', 'product'])
            ->whereHas('product', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->find($id);

        if (!$item) {
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

        if ($item->order) {
            $item->order->update(['status' => strtolower($request->status)]);
        }

        $mappedOrder = [
            'id'             => $item->id,
            'customer_name'  => $item->order->customer_name ?? 'N/A',
            'customer_email' => $item->order->customer_email ?? 'N/A',
            'product_id'     => $item->product_id,
            'vendor_id'      => $item->product->vendor_id ?? 0,
            'quantity'       => $item->quantity,
            'total_price'    => $item->line_total,
            'status'         => ucfirst($request->status),
            'created_at'     => $item->created_at,
            'updated_at'     => $item->updated_at,
            'product'        => $item->product,
        ];

        return response()->json([
            'status'  => 'success',
            'message' => 'Order status updated',
            'order'   => $mappedOrder,
        ]);
    }
}
