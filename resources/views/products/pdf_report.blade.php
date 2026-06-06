<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vendor Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

        /* Header */
        .header { background: #0f172a; color: white; padding: 24px 30px; margin-bottom: 24px; }
        .header h1 { font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
        .header .subtitle { color: #94a3b8; font-size: 11px; margin-top: 4px; }
        .header .meta { margin-top: 12px; font-size: 10px; color: #64748b; }

        /* Stats */
        .stats { display: table; width: 100%; border-collapse: separate; border-spacing: 8px; margin-bottom: 24px; }
        .stat { display: table-cell; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; width: 20%; text-align: center; }
        .stat-value { font-size: 22px; font-weight: 700; color: #0f172a; }
        .stat-label { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 3px; }

        /* Section */
        .section { margin-bottom: 28px; }
        .section-title { font-size: 13px; font-weight: 700; color: #0f172a; border-bottom: 2px solid #2563eb; padding-bottom: 6px; margin-bottom: 14px; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; }
        thead { background: #1e40af; color: white; }
        thead th { padding: 8px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 600; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

        /* Badges */
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 600; }
        .badge-approved { background: #dcfce7; color: #16a34a; }
        .badge-pending  { background: #fef9c3; color: #92400e; }
        .badge-rejected { background: #fee2e2; color: #dc2626; }
        .badge-delivered   { background: #dcfce7; color: #16a34a; }
        .badge-processing  { background: #dbeafe; color: #1d4ed8; }
        .badge-pending-ord { background: #fef9c3; color: #92400e; }

        /* Footer */
        .footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>

<div class="header">
    <h1>ShopMart — Vendor Report</h1>
    <div class="subtitle">Multi-Vendor E-Commerce System</div>
    <div class="meta">
        Generated: {{ now()->format('d M Y, h:i A') }}
    </div>
</div>

{{-- Stats --}}
<table class="stats">
    <tr>
        <td class="stat">
            <div class="stat-value">{{ $stats['total_products'] }}</div>
            <div class="stat-label">Total Products</div>
        </td>
        <td class="stat">
            <div class="stat-value" style="color:#16a34a;">{{ $stats['approved'] }}</div>
            <div class="stat-label">Approved</div>
        </td>
        <td class="stat">
            <div class="stat-value" style="color:#ca8a04;">{{ $stats['pending'] }}</div>
            <div class="stat-label">Pending</div>
        </td>
        <td class="stat">
            <div class="stat-value" style="color:#9333ea;">{{ $stats['total_orders'] }}</div>
            <div class="stat-label">Total Orders</div>
        </td>
        <td class="stat">
            <div class="stat-value" style="color:#2563eb;">PKR {{ number_format($stats['total_revenue'], 0) }}</div>
            <div class="stat-label">Total Revenue</div>
        </td>
    </tr>
</table>

{{-- Products table --}}
<div class="section">
    <div class="section-title">Product Inventory</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Price (PKR)</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $i => $product)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? '—' }}</td>
                <td>{{ number_format($product->price, 2) }}</td>
                <td>{{ $product->stock }}</td>
                <td>
                    <span class="badge badge-{{ strtolower($product->status) }}">{{ $product->status }}</span>
                </td>
                <td>{{ $product->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center; color:#94a3b8; padding:16px;">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Orders table --}}
<div class="section">
    <div class="section-title">Order History</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Total (PKR)</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $i => $order)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->product->name ?? '—' }}</td>
                <td>{{ $order->quantity }}</td>
                <td>{{ number_format($order->total_price, 2) }}</td>
                <td>
                    <span class="badge badge-{{ strtolower(str_replace(' ', '-', $order->status)) }}">{{ $order->status }}</span>
                </td>
                <td>{{ $order->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center; color:#94a3b8; padding:16px;">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="footer">
    This report was automatically generated by the ShopMart Vendor Panel &nbsp;|&nbsp;
    ShopMart Multi-Vendor E-Commerce &nbsp;|&nbsp; Confidential
</div>

</body>
</html>
