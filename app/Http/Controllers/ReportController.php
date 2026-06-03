<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index', $this->reportData());
    }

    public function download()
    {
        $pdf = Pdf::loadView('reports.pdf', $this->reportData());

        return $pdf->download('admin-panel-report.pdf');
    }

    protected function reportData(): array
    {
        return [
            'vendors' => Vendor::withCount('products')->latest()->get(),
            'products' => Product::with('vendor')->latest()->get(),
            'orders' => Order::with('items.product.vendor')->latest()->get(),
            'summary' => [
                'vendors' => Vendor::count(),
                'active_vendors' => Vendor::where('status', 'active')->count(),
                'products' => Product::count(),
                'approved_products' => Product::where('approval_status', 'approved')->count(),
                'pending_products' => Product::where('approval_status', 'pending')->count(),
                'orders' => Order::count(),
                'revenue' => Order::sum('total_amount'),
            ],
        ];
    }
}
