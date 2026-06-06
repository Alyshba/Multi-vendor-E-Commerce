<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with('product.category')
            ->get();

        $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
        $shipping = $subtotal >= 500 ? 0 : 50;
        $total = $subtotal + $shipping;

        return view('customer.cart.index', compact('cartItems', 'subtotal', 'shipping', 'total'));
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $product = Product::marketplaceVisible()->findOrFail($request->product_id);

        $existingQuantity = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->value('quantity') ?? 0;

        $newQuantity = $existingQuantity + (int) $request->quantity;

        if ($product->stock_quantity < $newQuantity) {
            return back()->with('error', 'Insufficient stock');
        }

        Cart::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $product->id],
            ['quantity' => $newQuantity]
        );

        return back()->with('success', 'Item added to cart');
    }

    public function update(Request $request, Cart $cart): RedirectResponse
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate(['quantity' => 'required|integer|min:1|max:99']);

        if (! $cart->product || ! $cart->product->is_active || $cart->product->approval_status !== 'approved') {
            return back()->with('error', 'Product is no longer available');
        }

        if ($cart->product->stock_quantity < $request->quantity) {
            return back()->with('error', 'Insufficient stock');
        }

        $cart->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Cart updated');
    }

    public function destroy(Cart $cart): RedirectResponse
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart->delete();
        return back()->with('success', 'Item removed from cart');
    }
}
