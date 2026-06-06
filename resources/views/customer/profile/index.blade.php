@extends('customer.layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-person"></i> My Profile</h2>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Profile Information</h5>
                    <form method="POST" action="{{ route('customer.profile.update') }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $user->address) }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-dark">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Change Password</h5>
                    <form method="POST" action="{{ route('customer.profile.password') }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-dark">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
