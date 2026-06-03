<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'vendorsCount' => Vendor::count(),
            'activeVendorsCount' => Vendor::where('status', 'active')->count(),
            'pendingProductsCount' => Product::where('approval_status', 'pending')->count(),
            'productsCount' => Product::count(),
            'ordersCount' => Order::count(),
            'revenue' => Order::whereIn('status', ['paid', 'shipped', 'completed'])->sum('total_amount'),
            'latestVendors' => Vendor::latest()->take(5)->get(),
            'latestProducts' => Product::with('vendor')->latest()->take(6)->get(),
        ]);
    }
}
