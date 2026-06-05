<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(): JsonResponse
    {
        $cartItems = Cart::where('user_id', auth('api')->id())
            ->with('product.category')
            ->get();

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        return response()->json([
            'items' => $cartItems,
            'total' => $total,
            'count' => $cartItems->sum('quantity'),
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::marketplaceVisible()->findOrFail($request->product_id);

        $existingQuantity = Cart::where('user_id', auth('api')->id())
            ->where('product_id', $product->id)
            ->value('quantity') ?? 0;

        $newQuantity = $existingQuantity + (int) $request->quantity;

        if ($product->stock_quantity < $newQuantity) {
            return response()->json(['message' => 'Insufficient stock'], 400);
        }

        $cartItem = Cart::updateOrCreate(
            ['user_id' => auth('api')->id(), 'product_id' => $product->id],
            ['quantity' => $newQuantity]
        );

        return response()->json([
            'message' => 'Item added to cart',
            'item' => $cartItem->load('product.category'),
        ]);
    }

    public function update(Request $request, Cart $cart): JsonResponse
    {
        if ($cart->user_id !== auth('api')->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (! $cart->product || ! $cart->product->is_active || $cart->product->approval_status !== 'approved') {
            return response()->json(['message' => 'Product is no longer available'], 400);
        }

        if ($cart->product->stock_quantity < $request->quantity) {
            return response()->json(['message' => 'Insufficient stock'], 400);
        }

        $cart->update(['quantity' => $request->quantity]);

        return response()->json([
            'message' => 'Cart updated',
            'item' => $cart->load('product.category'),
        ]);
    }

    public function destroy(Cart $cart): JsonResponse
    {
        if ($cart->user_id !== auth('api')->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cart->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }

    public function clear(): JsonResponse
    {
        Cart::where('user_id', auth('api')->id())->delete();
        return response()->json(['message' => 'Cart cleared']);
    }
}
