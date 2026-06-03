@extends('layouts.app')

@section('title', 'Vendors')

@section('content')
<div class="card table-card">
    <div class="card-header bg-white">
        <form class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Search</label>
                <input name="search" value="{{ request('search') }}" class="form-control" placeholder="Store, owner, or email">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    @foreach(['pending', 'active', 'suspended'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
            <div class="col-md-2"><a href="{{ route('vendors.create') }}" class="btn btn-primary w-100">Add Vendor</a></div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Store</th><th>Owner</th><th>Contact</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($vendors as $vendor)
                <tr>
                    <td><strong>{{ $vendor->name }}</strong><br><small class="text-muted">{{ $vendor->address }}</small></td>
                    <td>{{ $vendor->owner_name }}</td>
                    <td>{{ $vendor->email }}<br><small>{{ $vendor->phone }}</small></td>
                    <td><span class="badge text-bg-{{ $vendor->status === 'active' ? 'success' : ($vendor->status === 'suspended' ? 'danger' : 'warning') }}">{{ ucfirst($vendor->status) }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('vendors.destroy', $vendor) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this vendor?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No vendors found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $vendors->links() }}</div>
</div>
@endsection
