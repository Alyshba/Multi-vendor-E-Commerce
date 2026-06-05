<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $totalOrders = Order::where('user_id', $user->id)->count();
        $pendingOrders = Order::where('user_id', $user->id)->where('status', 'pending')->count();
        $totalSpent = Order::where('user_id', $user->id)->where('status', 'delivered')->sum('total');
        $recentOrders = Order::where('user_id', $user->id)->latest()->take(5)->get();

        return view('customer.dashboard', compact('user', 'totalOrders', 'pendingOrders', 'totalSpent', 'recentOrders'));
    }
}
