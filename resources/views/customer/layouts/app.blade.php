<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ShopEase') - ShopEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('customer.products') }}">
                <i class="bi bi-shop"></i> ShopEase
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer.products') }}">Products</a>
                    </li>
                    @auth
                    <li class="nav-item ms-2">
                        <form id="admin-logout-form" action="{{ route('customer.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="redirect" value="admin">
                            <button type="submit" class="btn btn-danger btn-sm px-3 fw-bold">
                                <i class="bi bi-shield-lock-fill me-1"></i> Logout → Admin Panel
                            </button>
                        </form>
                    </li>
                    @endauth
                </ul>
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('customer.cart') }}">
                            <i class="bi bi-cart3 fs-5"></i>
                            @php $cartCount = auth()->check() ? \App\Models\Cart::where('user_id', auth()->id())->sum('quantity') : 0 @endphp
                            @if($cartCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.orders') }}">My Orders</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.profile') }}">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('customer.logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('customer.login') }}">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('customer.register') }}">Register</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-0 mb-0" role="alert">
            <div class="container">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-0 mb-0" role="alert">
            <div class="container">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} ShopEase. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
