@extends('customer.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-credit-card"></i> Checkout</h2>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Shipping Details</h5>
                    <form method="POST" action="{{ route('customer.order.place') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Shipping Address *</label>
                            <textarea name="shipping_address" id="shipping_address" class="form-control @error('shipping_address') is-invalid @enderror" rows="3" required>{{ old('shipping_address', auth()->user()->address) }}</textarea>
                            @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method *</label>
                            <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                <option value="">Select payment method</option>
                                <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Cash on Delivery (COD)</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                            </select>
                            @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Order Notes (optional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-dark btn-lg">Place Order</button>
                            <a href="{{ route('customer.cart') }}" class="btn btn-outline-dark">Back to Cart</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Order Summary</h5>
                    @foreach($cartItems as $item)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <small class="fw-bold">{{ $item->product->name }}</small>
                                <small class="d-block text-muted">Qty: {{ $item->quantity }}</small>
                            </div>
                            <small class="fw-bold">Rs. {{ number_format($item->product->price * $item->quantity, 2) }}</small>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Subtotal</span>
                        <span>Rs. {{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Shipping</span>
                        <span>
                            @if($shipping == 0)
                                <span class="text-success">Free</span>
                            @else
                                Rs. {{ number_format($shipping, 2) }}
                            @endif
                        </span>
                    </div>
                    @if($shipping > 0)
                        <small class="text-muted d-block mb-2">Free shipping on orders over Rs. 500</small>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fs-5 fw-bold">Total</span>
                        <span class="fs-5 fw-bold text-danger">Rs. {{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
