@extends('layouts.app')

@section('title', 'Login')

@section('content')
<style>
    .login-shell {
        min-height: calc(100vh - 3rem);
        display: grid;
        place-items: center;
    }

    .login-panel {
        overflow: hidden;
        border: 0;
        border-radius: .5rem;
        box-shadow: 0 18px 48px rgba(15, 23, 42, .12);
    }

    .login-summary {
        min-height: 100%;
        color: #fff;
        background:
            linear-gradient(145deg, rgba(24, 34, 53, .94), rgba(13, 110, 253, .8)),
            url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=1100&q=80') center/cover;
    }

    .role-choice {
        position: relative;
    }

    .role-choice input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .role-card {
        height: 100%;
        cursor: pointer;
        border: 1px solid #dbe3ef;
        border-radius: .5rem;
        padding: .85rem;
        background: #fff;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }

    .role-choice input:checked + .role-card {
        border-color: #0d6efd;
        box-shadow: 0 8px 22px rgba(13, 110, 253, .16);
        transform: translateY(-1px);
    }

    .role-card strong {
        display: block;
        color: #182235;
        font-size: .95rem;
    }

    .role-card small {
        color: #64748b;
    }

    .credential-note {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: .5rem;
        color: #475569;
    }
</style>

@php
    $roleEmails = [
        'admin' => 'admin@example.com',
        'vendor' => 'vendor@example.com',
        'customer' => 'customer@example.com',
    ];
    $selectedRole = array_key_exists(old('role', 'vendor'), $roleEmails) ? old('role', 'vendor') : 'vendor';
@endphp

<div class="login-shell">
    <div class="col-12 col-xl-10">
        <div class="card login-panel">
            <div class="row g-0">
                <div class="col-lg-5">
                    <div class="login-summary p-4 p-lg-5 d-flex flex-column justify-content-between">
                        <div>
                            <span class="badge text-bg-light mb-3">Multi-Vendor E-Commerce</span>
                            <h1 class="h2 mb-3">Inventory, orders, vendors, and customers in one control panel.</h1>
                            <p class="mb-0 text-white-50">Choose the account type that matches your workflow before signing in.</p>
                        </div>
                        <div class="row g-3 mt-4">
                            <div class="col-6">
                                <div class="border border-light-subtle rounded p-3">
                                    <small class="text-white-50">Vendor</small>
                                    <div class="fw-semibold">Stock and products</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border border-light-subtle rounded p-3">
                                    <small class="text-white-50">Customer</small>
                                    <div class="fw-semibold">Orders and purchases</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card-body p-4 p-lg-5">
                        <h3 class="mb-1">Login Panel</h3>
                        <p class="text-muted mb-4">Select Vendor, Customer, or Admin to continue with the correct account type.</p>

                        <form method="POST" action="{{ route('login.submit') }}">
                            @csrf

                            <div class="row g-3 mb-4">
                                @foreach([
                                    'vendor' => ['Vendor', 'Manage catalog, inventory, and product approvals.'],
                                    'customer' => ['Customer', 'Track orders, purchases, and account activity.'],
                                    'admin' => ['Admin', 'Oversee vendors, products, reports, and orders.'],
                                ] as $role => [$label, $description])
                                    <div class="col-md-4">
                                        <label class="role-choice d-block">
                                            <input type="radio" name="role" value="{{ $role }}" data-email="{{ $roleEmails[$role] }}" @checked($selectedRole === $role)>
                                            <span class="role-card">
                                                <strong>{{ $label }}</strong>
                                                <small>{{ $description }}</small>
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input id="login-email" type="email" name="email" value="{{ old('email', $roleEmails[$selectedRole]) }}" class="form-control" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" value="password" class="form-control" required>
                            </div>
                            <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 mb-4">
                                <div class="form-check">
                                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                <small class="credential-note px-3 py-2">Demo password: <strong>password</strong></small>
                            </div>
                            <button class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('input[name="role"]').forEach((roleInput) => {
        roleInput.addEventListener('change', () => {
            const emailInput = document.getElementById('login-email');
            emailInput.value = roleInput.dataset.email;
        });
    });
</script>
@endsection
