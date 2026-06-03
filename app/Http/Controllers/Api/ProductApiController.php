<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function index()
    {
        return Product::with('vendor')->latest()->paginate(10);
    }

    public function store(Request $request)
    {
        $product = Product::create($this->validated($request));

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return $product->load('vendor');
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product->id));

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }

    public function approve(Product $product)
    {
        $product->update(['approval_status' => 'approved']);

        return response()->json($product);
    }

    public function reject(Product $product)
    {
        $product->update(['approval_status' => 'rejected']);

        return response()->json($product);
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'vendor_id' => ['required', 'exists:vendors,id'],
            'name' => ['required', 'string', 'max:150'],
            'sku' => ['required', 'string', 'max:80', 'unique:products,sku,'.($ignoreId ?? 'NULL')],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'approval_status' => ['required', 'in:pending,approved,rejected'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
