@extends('layouts.app')

@section('title', 'Vendor Details')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card table-card">
            <div class="card-body">
                <h5>{{ $vendor->name }}</h5>
                <p class="text-muted mb-2">{{ $vendor->owner_name }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $vendor->email }}</p>
                <p class="mb-1"><strong>Phone:</strong> {{ $vendor->phone }}</p>
                <p class="mb-1"><strong>Status:</strong> {{ ucfirst($vendor->status) }}</p>
                <p class="mb-0"><strong>Address:</strong> {{ $vendor->address }}</p>
                <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-primary mt-3">Edit Vendor</a>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card table-card">
            <div class="card-header bg-white"><strong>Vendor Products</strong></div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Product</th><th>SKU</th><th>Price</th><th>Stock</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($vendor->products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->sku }}</td>
                            <td>Rs {{ number_format($product->price, 2) }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>{{ ucfirst($product->approval_status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">No products assigned.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
