<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();

        return response()->json([
            'status'     => 'success',
            'categories' => $categories,
        ]);
    }

    public function show($id)
    {
        $category = Category::with('products')->find($id);

        if (!$category) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'status'   => 'success',
            'category' => $category,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Category created successfully',
            'category' => $category,
        ], 201);
    }
}
