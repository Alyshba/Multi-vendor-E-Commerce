@extends('customer.layouts.app')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.orders') }}" class="text-decoration-none">My Orders</a></li>
            <li class="breadcrumb-item active">{{ $order->order_number }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
                    @php
                        $statusColors = ['pending' => 'warning', 'processing' => 'info', 'shipped' => 'primary', 'delivered' => 'success', 'cancelled' => 'danger'];
                        $color = $statusColors[$order->status] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $color }} fs-6">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Order Date</small>
                            <strong>{{ $order->created_at->format('d M Y, h:i A') }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Payment Method</small>
                            <strong>{{ $order->payment_method == 'cod' ? 'Cash on Delivery' : 'Credit/Debit Card' }}</strong>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Payment Status</small>
                            <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">{{ ucfirst($order->payment_status) }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Shipping Address</small>
                            <strong>{{ $order->shipping_address }}</strong>
                        </div>
                    </div>
                    @if($order->notes)
                        <div class="mb-3">
                            <small class="text-muted d-block">Order Notes</small>
                            <p class="mb-0">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>Rs. {{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end fw-bold">Rs. {{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end">Subtotal</td>
                                <td class="text-end">Rs. {{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">Shipping</td>
                                <td class="text-end">Rs. {{ number_format($order->shipping, 2) }}</td>
                            </tr>
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">Total</td>
                                <td class="text-end text-danger fs-5">Rs. {{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Timeline</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <span class="badge bg-success rounded-circle p-2"><i class="bi bi-check"></i></span>
                                </div>
                                <div>
                                    <strong>Order Placed</strong>
                                    <small class="d-block text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</small>
                                </div>
                            </div>
                        </li>
                        @if(in_array($order->status, ['processing', 'shipped', 'delivered']))
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <span class="badge bg-{{ $order->status == 'processing' ? 'warning' : 'success' }} rounded-circle p-2"><i class="bi bi-gear"></i></span>
                                    </div>
                                    <div>
                                        <strong>Processing</strong>
                                        <small class="d-block text-muted">Order is being processed</small>
                                    </div>
                                </div>
                            </li>
                        @endif
                        @if(in_array($order->status, ['shipped', 'delivered']))
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <span class="badge bg-{{ $order->status == 'shipped' ? 'primary' : 'success' }} rounded-circle p-2"><i class="bi bi-truck"></i></span>
                                    </div>
                                    <div>
                                        <strong>Shipped</strong>
                                        <small class="d-block text-muted">Order has been shipped</small>
                                    </div>
                                </div>
                            </li>
                        @endif
                        @if($order->status == 'delivered')
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <span class="badge bg-success rounded-circle p-2"><i class="bi bi-check-circle"></i></span>
                                    </div>
                                    <div>
                                        <strong>Delivered</strong>
                                        <small class="d-block text-muted">Order delivered successfully</small>
                                    </div>
                                </div>
                            </li>
                        @endif
                        @if($order->status == 'cancelled')
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <span class="badge bg-danger rounded-circle p-2"><i class="bi bi-x"></i></span>
                                    </div>
                                    <div>
                                        <strong>Cancelled</strong>
                                        <small class="d-block text-muted">Order was cancelled</small>
                                    </div>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            @if(in_array($order->status, ['pending', 'processing']))
                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <form method="POST" action="{{ route('customer.order.cancel', $order) }}" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger w-100">Cancel Order</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
