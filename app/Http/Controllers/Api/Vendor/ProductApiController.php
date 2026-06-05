<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class ProductApiController extends Controller
{
    protected function getVendorId()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return 0;
        }
        $vendor = Vendor::where('user_id', $user->id)->first() ?? Vendor::where('email', $user->email)->first();
        return $vendor ? $vendor->id : 0;
    }

    public function index(Request $request)
    {
        $vendorId = $this->getVendorId();
        $query = Product::with('category')->where('vendor_id', $vendorId);

        if ($request->has('status')) {
            $query->where('approval_status', strtolower($request->status));
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(10);

        return response()->json([
            'status'   => 'success',
            'products' => $products,
        ]);
    }

    public function show($id)
    {
        $vendorId = $this->getVendorId();
        $product = Product::with('category')
            ->where('vendor_id', $vendorId)
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

    public function store(Request $request)
    {
        $vendorId = $this->getVendorId();

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
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

        $categoryObj = Category::find($request->category_id);
        $sku = 'VN-' . strtoupper(uniqid());

        $product = Product::create([
            'name'            => $request->name,
            'sku'             => $sku,
            'category'        => $categoryObj->name ?? 'General',
            'slug'            => Str::slug($request->name),
            'description'     => $request->description,
            'price'           => $request->price,
            'stock'           => $request->stock,
            'stock_quantity'  => $request->stock,
            'category_id'     => $request->category_id,
            'vendor_id'       => $vendorId,
            'approval_status' => 'pending',
            'is_active'       => false,
            'image'           => $imagePath,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product created successfully',
            'data'    => $product->load('category'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $vendorId = $this->getVendorId();
        $product = Product::where('vendor_id', $vendorId)->find($id);

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
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
        }

        $updateData = $request->only([
            'name', 'description', 'price', 'stock', 'category_id'
        ]);

        if (isset($updateData['category_id'])) {
            $categoryObj = Category::find($updateData['category_id']);
            $updateData['category'] = $categoryObj->name ?? 'General';
        }

        if (isset($updateData['stock'])) {
            $updateData['stock_quantity'] = $updateData['stock'];
        }

        if (isset($updateData['name'])) {
            $updateData['slug'] = Str::slug($updateData['name']);
        }

        $product->update($updateData);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product updated successfully',
            'data'    => $product->load('category'),
        ]);
    }

    public function destroy($id)
    {
        $vendorId = $this->getVendorId();
        $product = Product::where('vendor_id', $vendorId)->find($id);

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
