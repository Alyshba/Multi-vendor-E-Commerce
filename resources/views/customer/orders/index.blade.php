@extends('customer.layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-box"></i> My Orders</h2>

    @if($orders->count() > 0)
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td>{{ $order->items->count() }}</td>
                                <td class="fw-bold">Rs. {{ number_format($order->total, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = ['pending' => 'warning', 'processing' => 'info', 'shipped' => 'primary', 'delivered' => 'success', 'cancelled' => 'danger'];
                                        $color = $statusColors[$order->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('customer.order.show', $order) }}" class="btn btn-sm btn-outline-dark">
                                        View
                                    </a>
                                    @if(in_array($order->status, ['pending', 'processing']))
                                        <form method="POST" action="{{ route('customer.order.cancel', $order) }}" class="d-inline" onsubmit="return confirm('Cancel this order?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-box-seam display-1 text-muted"></i>
            <h4 class="text-muted mt-3">No orders yet</h4>
            <p class="text-muted">Start shopping to place your first order.</p>
            <a href="{{ route('customer.products') }}" class="btn btn-dark">Browse Products</a>
        </div>
    @endif
</div>
@endsection
