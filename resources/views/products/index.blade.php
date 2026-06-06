@extends('layouts.app')

@section('title', 'My Products')
@section('page-title', 'My Products')

@section('content')

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted" style="font-size:0.8rem;">Total Products</span>
                <div class="stat-icon" style="background:#eff6ff; color:#2563eb;">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold">{{ $stats['total'] }}</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted" style="font-size:0.8rem;">Approved</span>
                <div class="stat-icon" style="background:#f0fdf4; color:#16a34a;">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold">{{ $stats['approved'] }}</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted" style="font-size:0.8rem;">Pending</span>
                <div class="stat-icon" style="background:#fefce8; color:#ca8a04;">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold">{{ $stats['pending'] }}</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted" style="font-size:0.8rem;">Total Orders</span>
                <div class="stat-icon" style="background:#fdf4ff; color:#9333ea;">
                    <i class="bi bi-bag-check"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold">{{ $stats['orders'] }}</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius:12px;">

    <div class="card-header bg-white d-flex justify-content-between py-3">
        <h6 class="mb-0">Product Listing</h6>

        <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
            Add Product
        </a>
    </div>

    <div class="card-body p-0">

        @if($products->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($products as $i => $product)
                    <tr>
                        <td>{{ $products->firstItem() + $i }}</td>

                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                         alt="{{ $product->name }}" 
                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                                @else
                                    <div style="width: 40px; height: 40px; border-radius: 6px; background: #f1f5f9; border: 1px solid #e2e8f0; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size: 1rem;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                                <strong>{{ $product->name }}</strong>
                            </div>
                        </td>

                        <td>{{ $product->category->name ?? 'N/A' }}</td>

                        <td>PKR {{ number_format($product->price) }}</td>

                        <td>{{ $product->stock }}</td>

                        <td>
                            <span class="badge bg-secondary">
                                {{ $product->status }}
                            </span>
                        </td>

                        <td class="d-flex gap-1">

                            <a href="{{ route('products.edit', $product) }}"
                               class="btn btn-sm btn-outline-primary">
                                Edit
                            </a>

                            <a href="{{ route('products.delete', $product) }}"
                               class="btn btn-sm btn-danger">
                                Delete
                            </a>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- PAGINATION ADDED BACK --}}
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }}
            </small>

            {{ $products->links('pagination::bootstrap-5') }}
        </div>

        @else
            <div class="p-4 text-center text-muted">
                No products found
            </div>
        @endif

    </div>
</div>

@endsection