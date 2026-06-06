@extends('customer.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-grid"></i> Dashboard</h2>
        <span class="text-muted">Welcome back, <strong>{{ $user->name }}</strong></span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total Orders</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalOrders }}</h2>
                        </div>
                        <i class="bi bi-box-seam fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Pending Orders</h6>
                            <h2 class="mb-0 fw-bold">{{ $pendingOrders }}</h2>
                        </div>
                        <i class="bi bi-clock-history fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total Spent</h6>
                            <h4 class="mb-0 fw-bold">Rs. {{ number_format($totalSpent, 2) }}</h4>
                        </div>
                        <i class="bi bi-currency-rupee fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="{{ route('customer.orders') }}" class="btn btn-sm btn-outline-light">View All</a>
                </div>
                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td class="fw-bold">Rs. {{ number_format($order->total, 2) }}</td>
                                        <td>
                                            @php
                                                $statusColors = ['pending' => 'warning', 'processing' => 'info', 'shipped' => 'primary', 'delivered' => 'success', 'cancelled' => 'danger'];
                                                $color = $statusColors[$order->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">{{ ucfirst($order->status) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('customer.order.show', $order) }}" class="btn btn-sm btn-outline-dark">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="card-body text-center text-muted">
                        <p class="mb-0">No orders yet.</p>
                        <a href="{{ route('customer.products') }}" class="btn btn-dark mt-2">Start Shopping</a>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Profile Info</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Name:</strong> {{ $user->name }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
                    <p class="mb-0"><strong>Address:</strong> {{ $user->address ?? 'N/A' }}</p>
                    <hr>
                    <a href="{{ route('customer.profile') }}" class="btn btn-outline-dark w-100">
                        <i class="bi bi-pencil"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
