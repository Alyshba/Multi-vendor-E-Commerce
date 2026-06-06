@extends('vendor-panel.layouts.app')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="{{ route('vendor.products.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h5 class="mb-0 fw-semibold">Edit: {{ $product->name }}</h5>
            <span class="badge ms-2
                {{ $product->status == 'Approved' ? 'badge-approved' : ($product->status == 'Rejected' ? 'badge-rejected' : 'badge-pending') }}
                rounded-pill">
                {{ $product->status }}
            </span>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body p-4">

                @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('vendor.products.update', $product) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $product->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Description <span class="text-danger">*</span></label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Price (PKR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₨</span>
                                <input type="number" name="price" step="0.01" min="0"
                                    class="form-control @error('price') is-invalid @enderror"
                                    value="{{ old('price', $product->price) }}" required>
                            </div>
                            @error('price')<div class="text-danger" style="font-size:0.8rem;">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="stock" min="0"
                                class="form-control @error('stock') is-invalid @enderror"
                                value="{{ old('stock', $product->stock) }}" required>
                            @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">— Select Category —</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Product Image</label>
                            @if($product->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="Current" style="height:80px; border-radius:8px; border:1px solid #e2e8f0;">
                                <div class="text-muted mt-1" style="font-size:0.78rem;">Current image (upload new to replace)</div>
                            </div>
                            @endif
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                                accept="image/*" id="imageInput">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="mt-2" id="imagePreview" style="display:none;">
                                <img id="previewImg" src="" alt="New image" style="max-height:100px; border-radius:8px; border:1px solid #e2e8f0;">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2-circle me-2"></i>Save Changes
                        </button>
                        <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush