@extends('customer.layouts.app')

@section('title', 'Products')

@push('styles')
<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .product-img {
        height: 220px;
        object-fit: cover;
    }
    .product-price {
        font-size: 1.2rem;
        font-weight: 700;
        color: #dc3545;
    }
    .compare-price {
        text-decoration: line-through;
        color: #999;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="bg-light py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Categories</h5>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('customer.products') }}" class="list-group-item list-group-item-action px-0 border-0 {{ !request('category') ? 'fw-bold text-dark' : '' }}">
                                All Categories
                            </a>
                            @foreach($categories as $cat)
                                <a href="{{ route('customer.products', ['category' => $cat->id]) }}"
                                   class="list-group-item list-group-item-action px-0 border-0 d-flex justify-content-between align-items-center
                                   {{ request('category') == $cat->id ? 'fw-bold text-dark' : '' }}">
                                    {{ $cat->name }}
                                    <span class="badge bg-secondary rounded-pill">{{ $cat->products_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Price Range</h5>
                        <form method="GET" action="{{ route('customer.products') }}">
                            @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                            @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                            <div class="mb-2">
                                <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" value="{{ request('min_price') }}">
                            </div>
                            <div class="mb-2">
                                <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" value="{{ request('max_price') }}">
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-dark w-100">Filter</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form method="GET" action="{{ route('customer.products') }}" class="d-flex w-50">
                        @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                            <button class="btn btn-outline-dark" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                    <div class="d-flex align-items-center">
                        <label class="me-2 text-muted small">Sort:</label>
                        <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href=this.value">
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ $sort == 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ $sort == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ $sort == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'name']) }}" {{ $sort == 'name' ? 'selected' : '' }}>Name</option>
                        </select>
                    </div>
                </div>

                @if($products->count() > 0)
                    <div class="row g-3">
                        @foreach($products as $product)
                            <div class="col-md-4 col-6">
                                <div class="card product-card h-100 shadow-sm border-0">
                                    <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x220?text=No+Image' }}"
                                         class="card-img-top product-img" alt="{{ $product->name }}">
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title mb-1">
                                            <a href="{{ route('customer.product.show', $product->slug) }}" class="text-decoration-none text-dark stretched-link">
                                                {{ $product->name }}
                                            </a>
                                        </h6>
                                            <span class="badge bg-light text-dark align-self-start mb-2">{{ is_string($product->category) ? $product->category : ($product->category->name ?? '') }}</span>
                                        <div class="mt-auto">
                                            <div class="product-price">
                                                Rs. {{ number_format($product->price, 2) }}
                                                @if($product->compare_price)
                                                    <span class="compare-price">Rs. {{ number_format($product->compare_price, 2) }}</span>
                                                @endif
                                            </div>
                                            @if($product->stock_quantity > 0)
                                                <span class="badge bg-success">In Stock</span>
                                            @else
                                                <span class="badge bg-danger">Out of Stock</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-box-seam display-1 text-muted"></i>
                        <p class="text-muted mt-3">No products found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
