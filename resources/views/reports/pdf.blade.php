<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Panel Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1, h2 { margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        th, td { border: 1px solid #d1d5db; padding: 7px; text-align: left; }
        th { background: #eef2ff; }
        .summary { display: table; width: 100%; margin-bottom: 18px; }
        .summary div { display: table-cell; border: 1px solid #d1d5db; padding: 9px; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <h1>Multi-Vendor Admin Panel Report</h1>
    <p class="muted">Generated on {{ now()->format('d M Y h:i A') }}</p>

    <div class="summary">
        <div><strong>Vendors</strong><br>{{ $summary['vendors'] }}</div>
        <div><strong>Products</strong><br>{{ $summary['products'] }}</div>
        <div><strong>Pending Products</strong><br>{{ $summary['pending_products'] }}</div>
        <div><strong>Total Revenue</strong><br>Rs {{ number_format($summary['revenue'], 2) }}</div>
    </div>

    <h2>Vendors</h2>
    <table>
        <thead><tr><th>Name</th><th>Owner</th><th>Email</th><th>Status</th><th>Products</th></tr></thead>
        <tbody>
        @foreach($vendors as $vendor)
            <tr><td>{{ $vendor->name }}</td><td>{{ $vendor->owner_name }}</td><td>{{ $vendor->email }}</td><td>{{ ucfirst($vendor->status) }}</td><td>{{ $vendor->products_count }}</td></tr>
        @endforeach
        </tbody>
    </table>

    <h2>Products and Inventory</h2>
    <table>
        <thead><tr><th>Product</th><th>Vendor</th><th>SKU</th><th>Price</th><th>Stock</th><th>Approval</th></tr></thead>
        <tbody>
        @foreach($products as $product)
            <tr><td>{{ $product->name }}</td><td>{{ $product->vendor?->name }}</td><td>{{ $product->sku }}</td><td>Rs {{ number_format($product->price, 2) }}</td><td>{{ $product->stock }}</td><td>{{ ucfirst($product->approval_status) }}</td></tr>
        @endforeach
        </tbody>
    </table>

    <h2>Orders</h2>
    <table>
        <thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Total</th><th>Date</th></tr></thead>
        <tbody>
        @forelse($orders as $order)
            <tr><td>{{ $order->order_number }}</td><td>{{ $order->customer_name }}</td><td>{{ ucfirst($order->status) }}</td><td>Rs {{ number_format($order->total_amount, 2) }}</td><td>{{ $order->created_at->format('d M Y') }}</td></tr>
        @empty
            <tr><td colspan="5">No orders available.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
