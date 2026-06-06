@extends('customer.layouts.app')

@section('title', $product->name)

@push('styles')
<style>
    .product-image-main {
        max-height: 450px;
        object-fit: contain;
    }
    .product-detail-price {
        font-size: 1.8rem;
        font-weight: 700;
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.products') }}" class="text-decoration-none">Products</a></li>
            @if($product->category)
                <li class="breadcrumb-item"><a href="{{ route('customer.products', ['category' => $product->category_id]) }}" class="text-decoration-none">{{ is_string($product->category) ? $product->category : ($product->category->name ?? '') }}</a></li>
            @endif
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/450x450?text=No+Image' }}"
                     class="card-img-top product-image-main" alt="{{ $product->name }}">
            </div>
        </div>
        <div class="col-md-7">
            <h2 class="fw-bold">{{ $product->name }}</h2>
            @if($product->category)
                <span class="badge bg-secondary mb-2">{{ is_string($product->category) ? $product->category : ($product->category->name ?? '') }}</span>
            @endif
            <hr>
            <div class="product-detail-price mb-2">
                Rs. {{ number_format($product->price, 2) }}
                @if($product->compare_price)
                    <span class="text-decoration-line-through text-muted fs-5 ms-2">Rs. {{ number_format($product->compare_price, 2) }}</span>
                    <span class="badge bg-danger ms-2">{{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}% OFF</span>
                @endif
            </div>
            <p class="text-muted mb-2">SKU: {{ $product->sku ?? 'N/A' }}</p>

            @if($product->stock_quantity > 0)
                <span class="badge bg-success fs-6 mb-3">In Stock ({{ $product->stock_quantity }} available)</span>
            @else
                <span class="badge bg-danger fs-6 mb-3">Out of Stock</span>
            @endif

            <div class="mb-4">
                <h6>Description</h6>
                <p class="text-muted">{{ $product->description ?? 'No description available.' }}</p>
            </div>

            @if($product->stock_quantity > 0)
                <form method="POST" action="{{ route('customer.cart.add') }}" class="d-flex gap-2 align-items-end">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div>
                        <label class="form-label small">Quantity</label>
                        <input type="number" name="quantity" class="form-control" value="1" min="1" max="{{ min($product->stock_quantity, 99) }}" style="width: 80px;">
                    </div>
                    <button type="submit" class="btn btn-dark btn-lg">
                        <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                </form>
            @endif

            <div class="mt-3 d-flex gap-2">
                <a href="{{ route('customer.cart') }}" class="btn btn-outline-dark">
                    <i class="bi bi-cart3"></i> View Cart
                </a>
            </div>
        </div>
    </div>

    @if($relatedProducts->count() > 0)
        <hr class="my-4">
        <h4 class="mb-3">Related Products</h4>
        <div class="row g-3">
            @foreach($relatedProducts as $related)
                <div class="col-md-3 col-6">
                    <div class="card product-card h-100 shadow-sm border-0">
                        <img src="{{ $related->image ? asset('storage/' . $related->image) : 'https://via.placeholder.com/300x220?text=No+Image' }}"
                             class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $related->name }}">
                        <div class="card-body">
                            <h6 class="card-title">
                                <a href="{{ route('customer.product.show', $related->slug) }}" class="text-decoration-none text-dark">{{ $related->name }}</a>
                            </h6>
                            <div class="fw-bold text-danger">Rs. {{ number_format($related->price, 2) }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
