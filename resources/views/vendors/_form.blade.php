@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Store Name</label>
        <input name="name" class="form-control" value="{{ old('name', $vendor->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Owner Name</label>
        <input name="owner_name" class="form-control" value="{{ old('owner_name', $vendor->owner_name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $vendor->email ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input name="phone" class="form-control" value="{{ old('phone', $vendor->phone ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            @foreach(['pending', 'active', 'suspended'] as $status)
                <option value="{{ $status }}" @selected(old('status', $vendor->status ?? 'pending') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Address</label>
        <textarea name="address" rows="3" class="form-control" required>{{ old('address', $vendor->address ?? '') }}</textarea>
    </div>
</div>
<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary">Save Vendor</button>
    <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
