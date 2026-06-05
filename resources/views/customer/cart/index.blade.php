@extends('customer.layouts.app')

@section('title', 'Cart')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-cart3"></i> Your Cart</h2>

    @if($cartItems->count() > 0)
        <div class="row g-4">
            <div class="col-lg-8">
                @foreach($cartItems as $item)
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2 col-3">
                                    <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : 'https://via.placeholder.com/100x100?text=No+Image' }}"
                                         class="img-fluid rounded" alt="{{ $item->product->name }}">
                                </div>
                                <div class="col-md-4 col-9">
                                    <h6 class="mb-1">{{ $item->product->name }}</h6>
                                    <small class="text-muted">{{ is_string($item->product->category) ? $item->product->category : ($item->product->category->name ?? '') }}</small>
                                    <div class="fw-bold text-danger mt-1">Rs. {{ number_format($item->product->price, 2) }}</div>
                                </div>
                                <div class="col-md-3 col-6 mt-2 mt-md-0">
                                    <form method="POST" action="{{ route('customer.cart.update', $item) }}" class="d-flex align-items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="99" class="form-control form-control-sm" style="width: 70px;">
                                        <button type="submit" class="btn btn-sm btn-outline-dark"><i class="bi bi-arrow-repeat"></i></button>
                                    </form>
                                </div>
                                <div class="col-md-2 col-3 mt-2 mt-md-0 text-end">
                                    <div class="fw-bold">Rs. {{ number_format($item->product->price * $item->quantity, 2) }}</div>
                                </div>
                                <div class="col-md-1 col-3 mt-2 mt-md-0 text-end">
                                    <form method="POST" action="{{ route('customer.cart.remove', $item) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Order Summary</h5>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span class="fw-bold">Rs. {{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <span class="fw-bold">
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
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fs-5">Total</span>
                            <span class="fs-5 fw-bold text-danger">Rs. {{ number_format($total, 2) }}</span>
                        </div>
                        <a href="{{ route('customer.checkout') }}" class="btn btn-dark w-100 btn-lg">
                            Proceed to Checkout
                        </a>
                        <a href="{{ route('customer.products') }}" class="btn btn-outline-dark w-100 mt-2">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h4 class="text-muted mt-3">Your cart is empty</h4>
            <p class="text-muted">Browse our products and add items to your cart.</p>
            <a href="{{ route('customer.products') }}" class="btn btn-dark">Start Shopping</a>
        </div>
    @endif
</div>
@endsection
