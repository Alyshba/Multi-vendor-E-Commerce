<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel') - Multi Vendor Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fb; }
        .sidebar { min-height: 100vh; background: #182235; }
        .sidebar a { color: #cbd5e1; text-decoration: none; display: block; padding: .75rem 1rem; border-radius: .5rem; }
        .sidebar a.active, .sidebar a:hover { background: #26344d; color: #fff; }
        .stat-card { border: 0; border-radius: .75rem; box-shadow: 0 8px 24px rgba(15, 23, 42, .06); }
        .table-card { border: 0; border-radius: .75rem; box-shadow: 0 8px 24px rgba(15, 23, 42, .05); }
        .badge-soft { background: #e8f1ff; color: #0d6efd; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        @auth
            <aside class="col-lg-2 col-md-3 sidebar p-3">
                <h5 class="text-white mb-4">Vendor Admin</h5>
                <nav class="d-grid gap-1">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('vendors.index') }}" class="{{ request()->routeIs('vendors.*') ? 'active' : '' }}">Vendors</a>
                    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Products</a>
                    <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">Reports</a>
                </nav>
            </aside>
        @endauth
        <main class="@auth col-lg-10 col-md-9 @else col-12 @endauth p-4">
            @auth
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="mb-0">@yield('title', 'Dashboard')</h3>
                        <small class="text-muted">{{ ucfirst(auth()->user()->role) }} control for vendors, inventory, orders, and reports.</small>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm">Logout</button>
                    </form>
                </div>
            @endauth

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
