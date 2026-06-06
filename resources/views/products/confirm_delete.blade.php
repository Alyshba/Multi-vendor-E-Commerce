@extends('layouts.app')

@section('title', 'Delete Product')
@section('page-title', 'Delete Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm text-center p-4" style="border-radius:12px;">
            <div class="card-body">
                <div class="text-danger mb-3" style="font-size: 3.5rem;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                
                <h4 class="fw-bold mb-3">Delete Product</h4>
                <p class="text-muted mb-4">
                    Are you sure you want to delete the product <strong>"{{ $product->name }}"</strong>? This action cannot be undone.
                </p>

                <div class="p-3 bg-light rounded-3 mb-4 text-start">
                    <div class="row g-2">
                        <div class="col-4 text-muted">Category:</div>
                        <div class="col-8 fw-semibold">{{ $product->category->name ?? 'N/A' }}</div>
                        
                        <div class="col-4 text-muted">Price:</div>
                        <div class="col-8 fw-semibold text-primary">PKR {{ number_format($product->price) }}</div>
                        
                        <div class="col-4 text-muted">Stock:</div>
                        <div class="col-8 fw-semibold">{{ $product->stock }} units</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('products.destroy_confirm', $product->id) }}">
                    @csrf
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="bi bi-trash-fill me-1"></i>Yes, Delete Product
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary px-4">
                            Cancel & Go Back
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
