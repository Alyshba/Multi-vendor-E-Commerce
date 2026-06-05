@extends('vendor-panel.layouts.app')

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

        <a href="{{ route('vendor.products.create') }}" class="btn btn-sm btn-primary">
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
                            <strong>{{ $product->name }}</strong>
                        </td>

                        <td>{{ is_string($product->category) ? $product->category : ($product->category->name ?? 'N/A') }}</td>

                        <td>PKR {{ number_format($product->price) }}</td>

                        <td>{{ $product->stock }}</td>

                        <td>
                            <span class="badge bg-secondary">
                                {{ $product->status }}
                            </span>
                        </td>

                        <td class="d-flex gap-1">

                             <a href="{{ route('vendor.products.edit', $product) }}"
                               class="btn btn-sm btn-outline-primary">
                                Edit
                            </a>



                             <form method="POST"
                                   action="{{ route('vendor.products.destroy', $product) }}"
                                   id="delete-form-{{ $product->id }}"
                                   style="display:inline;">
                                 @csrf
                                 @method('DELETE')
                                 <button type="button"
                                         class="btn btn-sm btn-danger"
                                         onclick="if(window.confirm('Are you sure you want to delete this product?')) { document.getElementById('delete-form-{{ $product->id }}').submit(); }">
                                     Delete
                                 </button>
                             </form>

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