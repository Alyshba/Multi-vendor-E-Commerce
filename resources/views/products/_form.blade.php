@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Vendor</label>
        <select name="vendor_id" class="form-select" required>
            <option value="">Select vendor</option>
            @foreach($vendors as $vendor)
                <option value="{{ $vendor->id }}" @selected(old('vendor_id', $product->vendor_id ?? '') == $vendor->id)>{{ $vendor->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Product Name</label>
        <input name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">SKU</label>
        <input name="sku" class="form-control" value="{{ old('sku', $product->sku ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Category</label>
        <input name="category" class="form-control" value="{{ old('category', $product->category ?? '') }}" required>
    </div>
    <div class="col-md-2">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', $product->price ?? '') }}" required>
    </div>
    <div class="col-md-2">
        <label class="form-label">Stock</label>
        <input type="number" min="0" name="stock" class="form-control" value="{{ old('stock', $product->stock ?? 0) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Approval Status</label>
        <select name="approval_status" class="form-select" required>
            @foreach(['pending', 'approved', 'rejected'] as $status)
                <option value="{{ $status }}" @selected(old('approval_status', $product->approval_status ?? 'pending') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
</div>
<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary">Save Product</button>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
