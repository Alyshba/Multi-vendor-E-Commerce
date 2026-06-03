@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('reports.download') }}" class="btn btn-primary">Download PDF Report</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><small class="text-muted">Total Vendors</small><h3>{{ $summary['vendors'] }}</h3></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><small class="text-muted">Approved Products</small><h3>{{ $summary['approved_products'] }}</h3></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><small class="text-muted">Orders</small><h3>{{ $summary['orders'] }}</h3></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><small class="text-muted">Revenue</small><h3>Rs {{ number_format($summary['revenue']) }}</h3></div></div></div>
</div>

<div class="card table-card mb-4">
    <div class="card-header bg-white"><strong>Vendor Summary</strong></div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Vendor</th><th>Owner</th><th>Status</th><th>Products</th></tr></thead>
            <tbody>
            @foreach($vendors as $vendor)
                <tr><td>{{ $vendor->name }}</td><td>{{ $vendor->owner_name }}</td><td>{{ ucfirst($vendor->status) }}</td><td>{{ $vendor->products_count }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card table-card">
    <div class="card-header bg-white"><strong>Inventory and Approval Summary</strong></div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Product</th><th>Vendor</th><th>Price</th><th>Stock</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($products as $product)
                <tr><td>{{ $product->name }}</td><td>{{ $product->vendor?->name }}</td><td>Rs {{ number_format($product->price, 2) }}</td><td>{{ $product->stock }}</td><td>{{ ucfirst($product->approval_status) }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
