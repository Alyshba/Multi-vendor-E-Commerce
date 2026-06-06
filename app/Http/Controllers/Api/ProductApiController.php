<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductApiController extends Controller
{
    // ─── GET /api/products ────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $vendor = JWTAuth::parseToken()->authenticate();

        $query = Product::with('category')
            ->where('vendor_id', $vendor->id);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(10);

        return response()->json([
            'status'   => 'success',
            'products' => $products,
        ]);
    }

    // ─── GET /api/products/{id} ───────────────────────────────────────────────

    public function show($id)
    {
        $vendor  = JWTAuth::parseToken()->authenticate();
        $product = Product::with('category')
            ->where('vendor_id', $vendor->id)
            ->find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'product' => $product,
        ]);
    }

    // ─── POST /api/products ───────────────────────────────────────────────────

    public function store(Request $request)
    {
        $vendor = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status'      => 'in:Pending,Approved,Rejected',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'category_id' => $request->category_id,
            'vendor_id'   => $vendor->id,
            'status'      => $request->status ?? 'Pending',
            'image'       => $imagePath,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product created successfully',
            'data'    => $product->load('category'),
        ], 201);
    }

    // ─── PUT /api/products/{id} ───────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $vendor  = JWTAuth::parseToken()->authenticate();
        $product = Product::where('vendor_id', $vendor->id)->find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Product not found or unauthorized',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price'       => 'sometimes|required|numeric|min:0',
            'stock'       => 'sometimes|required|integer|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'status'      => 'in:Pending,Approved,Rejected',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->fill($request->only([
            'name', 'description', 'price', 'stock', 'category_id', 'status'
        ]));
        $product->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Product updated successfully',
            'data'    => $product->load('category'),
        ]);
    }

    // ─── DELETE /api/products/{id} ────────────────────────────────────────────

    public function destroy($id)
    {
        $vendor  = JWTAuth::parseToken()->authenticate();
        $product = Product::where('vendor_id', $vendor->id)->find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Product not found or unauthorized',
            ], 404);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Product deleted successfully',
        ]);
    }
}
