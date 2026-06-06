<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::withCount('products')->get();
        return response()->json($categories);
    }

    public function show($slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->with('products')->firstOrFail();
        return response()->json($category);
    }
}
