<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductApiController extends Controller
{
    /**
     * Display a listing of products with dynamic filtering and pagination.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if (optional($request->user())->role !== 'admin') {
            $query->marketplaceVisible();
        }

        
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

    public function store(Request $request)
    {
        $product = Product::create($this->validatedProductData($request));

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json($product->load('vendor'));
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validatedProductData($request, $product->id));

        return response()->json($product->fresh());
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted.']);
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

    public function approve(Product $product)
    {
        $product->update([
            'approval_status' => 'approved',
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Product approved.',
            'product' => $product->fresh(),
        ]);
    }

    public function reject(Product $product)
    {
        $product->update([
            'approval_status' => 'rejected',
            'is_active' => false,
        ]);

        return response()->json([
            'message' => 'Product rejected.',
            'product' => $product->fresh(),
        ]);
    }

    protected function validatedProductData(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'vendor_id' => ['required', 'exists:vendors,id'],
            'name' => ['required', 'string', 'max:150'],
            'sku' => ['required', 'string', 'max:80', 'unique:products,sku,'.($ignoreId ?? 'NULL')],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'approval_status' => ['required', 'in:pending,approved,rejected'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['slug'] = Str::slug($data['name'] . '-' . $data['sku']);
        $data['stock_quantity'] = $data['stock'];
        $data['is_active'] = $data['approval_status'] === 'approved';

        return $data;
    }
}
