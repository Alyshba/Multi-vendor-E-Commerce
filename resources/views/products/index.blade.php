@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="card table-card">
    <div class="card-header bg-white">
        <form class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input name="search" value="{{ request('search') }}" class="form-control" placeholder="Name, SKU, category">
            </div>
            <div class="col-md-4">
                <label class="form-label">Vendor</label>
                <select name="vendor_id" class="form-select">
                    <option value="">All vendors</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" @selected(request('vendor_id') == $vendor->id)>{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Approval</label>
                <select name="approval_status" class="form-select">
                    <option value="">All</option>
                    @foreach(['pending', 'approved', 'rejected'] as $status)
                        <option value="{{ $status }}" @selected(request('approval_status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Product</th><th>Vendor</th><th>Inventory</th><th>Approval</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($products as $product)
                <tr>
                    <td><strong>{{ $product->name }}</strong><br><small class="text-muted">{{ $product->sku }} | {{ $product->category }}</small></td>
                    <td>{{ $product->vendor?->name }}</td>
                    <td>Rs {{ number_format($product->price, 2) }}<br><small>{{ $product->stock }} in stock</small></td>
                    <td><span class="badge text-bg-{{ $product->approval_status === 'approved' ? 'success' : ($product->approval_status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($product->approval_status) }}</span></td>
                    <td class="text-end">
                        @if($product->approval_status !== 'approved')
                            <form method="POST" action="{{ route('products.approve', $product) }}" class="d-inline">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-success">Approve</button></form>
                        @endif
                        @if($product->approval_status !== 'rejected')
                            <form method="POST" action="{{ route('products.reject', $product) }}" class="d-inline">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-warning">Reject</button></form>
                        @endif
                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline" onsubmit="return confirm('Delete this product?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No products found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $products->links() }}</div>
</div>
@endsection
