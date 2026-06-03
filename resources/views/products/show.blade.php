@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="card table-card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h4>{{ $product->name }}</h4>
                <p class="text-muted">{{ $product->sku }} | {{ $product->category }}</p>
            </div>
            <span class="badge text-bg-{{ $product->approval_status === 'approved' ? 'success' : ($product->approval_status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($product->approval_status) }}</span>
        </div>
        <div class="row mt-3">
            <div class="col-md-3"><strong>Vendor</strong><p>{{ $product->vendor?->name }}</p></div>
            <div class="col-md-3"><strong>Price</strong><p>Rs {{ number_format($product->price, 2) }}</p></div>
            <div class="col-md-3"><strong>Stock</strong><p>{{ $product->stock }}</p></div>
            <div class="col-md-3"><strong>Created</strong><p>{{ $product->created_at->format('d M Y') }}</p></div>
        </div>
        <strong>Description</strong>
        <p>{{ $product->description ?: 'No description provided.' }}</p>
        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Edit Product</a>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</div>
@endsection
