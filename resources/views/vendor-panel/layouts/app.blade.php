<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Vendor Panel') — ShopMart</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 250px;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --sidebar-bg: #0f172a;
            --sidebar-text: #94a3b8;
            --sidebar-hover: #1e293b;
            --sidebar-active: #2563eb;
        }

        body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; }

        /* Sidebar */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            overflow-y: auto;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid #1e293b;
        }
        .sidebar-brand h4 {
            color: #fff;
            margin: 0;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .sidebar-brand span { color: var(--primary); }
        .sidebar-nav { padding: 1rem 0; flex: 1; }
        .nav-label {
            padding: 0.5rem 1.25rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #475569;
            margin-top: 0.5rem;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1.25rem;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.9rem;
            border-left: 3px solid transparent;
        }
        .sidebar-link:hover {
            background: var(--sidebar-hover);
            color: #e2e8f0;
        }
        .sidebar-link.active {
            background: rgba(37,99,235,0.15);
            color: #60a5fa;
            border-left-color: var(--primary);
        }
        .sidebar-link i { font-size: 1rem; width: 20px; text-align: center; }
        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid #1e293b;
        }
        .user-badge {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #cbd5e1;
            font-size: 0.85rem;
        }
        .user-avatar {
            width: 36px; height: 36px;
            background: var(--primary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .topbar {
            background: white;
            padding: 0.875rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .page-content { padding: 1.5rem; }

        /* Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
        }

        /* Table */
        .table th { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border-bottom-width: 1px; }
        .table td { vertical-align: middle; font-size: 0.9rem; }
        .product-img { width: 44px; height: 44px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; }
        .product-img-placeholder { width: 44px; height: 44px; border-radius: 8px; background: #f1f5f9; border: 1px solid #e2e8f0; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size: 1.1rem; }

        /* Badges */
        .badge-approved { background: #dcfce7; color: #16a34a; }
        .badge-pending  { background: #fef9c3; color: #ca8a04; }
        .badge-rejected { background: #fee2e2; color: #dc2626; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

@auth
<!-- Sidebar -->
@if(Auth::check())
<aside class="sidebar">

    <div class="sidebar-brand">
        <h4><span>Shop</span>Mart</h4>
        <small style="color:#64748b;">Vendor Panel</small>
    </div>

    <a href="{{ route('vendor.products.index') }}" class="sidebar-link {{ request()->routeIs('vendor.products.index') ? 'active' : '' }}">
        <i class="bi bi-box"></i> Products
    </a>

    <a href="{{ route('vendor.products.create') }}" class="sidebar-link {{ request()->routeIs('vendor.products.create') ? 'active' : '' }}">
        <i class="bi bi-plus"></i> Add Product
    </a>

    <a href="{{ route('vendor.products.pdf') }}" class="sidebar-link">
        <i class="bi bi-file-earmark-pdf"></i> Download Report
    </a>

    <form method="POST" action="{{ route('vendor.logout') }}" class="mt-auto p-3">
        @csrf
        <button class="btn btn-sm btn-dark w-100">
            Logout
        </button>
    </form>

</aside>
@endif
@endauth

<!-- Main content -->
<div class="{{ Auth::check() ? 'main-content' : '' }}">

    @auth
    <div class="topbar">
        <div>
            <span style="font-weight:600; color:#0f172a;">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted" style="font-size:0.85rem;"><i class="bi bi-calendar3 me-1"></i>{{ now()->format('d M Y') }}</span>
            <a href="{{ route('vendor.products.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Add Product
            </a>
        </div>
    </div>
    @endauth

    <div class="{{ Auth::check() ? 'page-content' : '' }}">

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
