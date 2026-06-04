<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductApiController extends Controller
{
    /**
     * Display a listing of products with dynamic filtering and pagination.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        
        if ($request->has('status') && $request->status != '') {
            $query->where('approval_status', $request->status);
        }

        
        if ($request->has('in_stock') && $request->in_stock == 'true') {
            $query->where('stock', '>', 0);
        }

        
        $products = $query->paginate(10);

        return response()->json($products);
    }

    /**
     * Aggregate database counts to feed the Admin Reports Charts.
     */
    public function chartData()
    {
        
        $categoryDistribution = Product::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get();

        
        $statusBreakdown = Product::select('approval_status', DB::raw('count(*) as total'))
            ->groupBy('approval_status')
            ->get();

        return response()->json([
            'categories' => $categoryDistribution,
            'status_overview' => $statusBreakdown
        ]);
    }
}
