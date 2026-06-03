@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4 col-xl-2"><div class="card stat-card"><div class="card-body"><small class="text-muted">Vendors</small><h3>{{ $vendorsCount }}</h3></div></div></div>
    <div class="col-md-4 col-xl-2"><div class="card stat-card"><div class="card-body"><small class="text-muted">Active Vendors</small><h3>{{ $activeVendorsCount }}</h3></div></div></div>
    <div class="col-md-4 col-xl-2"><div class="card stat-card"><div class="card-body"><small class="text-muted">Products</small><h3>{{ $productsCount }}</h3></div></div></div>
    <div class="col-md-4 col-xl-2"><div class="card stat-card"><div class="card-body"><small class="text-muted">Pending Approval</small><h3>{{ $pendingProductsCount }}</h3></div></div></div>
    <div class="col-md-4 col-xl-2"><div class="card stat-card"><div class="card-body"><small class="text-muted">Orders</small><h3>{{ $ordersCount }}</h3></div></div></div>
    <div class="col-md-4 col-xl-2"><div class="card stat-card"><div class="card-body"><small class="text-muted">Revenue</small><h3>Rs {{ number_format($revenue) }}</h3></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card table-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Latest Vendors</strong>
                <a href="{{ route('vendors.create') }}" class="btn btn-sm btn-primary">Add Vendor</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Name</th><th>Owner</th><th>Status</th></tr></thead>
                    <tbody>
                    @foreach($latestVendors as $vendor)
                        <tr>
                            <td>{{ $vendor->name }}</td>
                            <td>{{ $vendor->owner_name }}</td>
                            <td><span class="badge text-bg-{{ $vendor->status === 'active' ? 'success' : ($vendor->status === 'suspended' ? 'danger' : 'warning') }}">{{ ucfirst($vendor->status) }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card table-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Recent Products</strong>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">Review Products</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Product</th><th>Vendor</th><th>Stock</th><th>Status</th></tr></thead>
                    <tbody>
                    @foreach($latestProducts as $product)
                        <tr>
                            <td>{{ $product->name }}<br><small class="text-muted">{{ $product->sku }}</small></td>
                            <td>{{ $product->vendor?->name }}</td>
                            <td>{{ $product->stock }}</td>
                            <td><span class="badge text-bg-{{ $product->approval_status === 'approved' ? 'success' : ($product->approval_status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($product->approval_status) }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
