<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Get all active categories with product counts.
     */
    public function index(): JsonResponse
    {
        $categories = Category::withCount('products')->get()->map(function ($c) {
            return [
                'id'             => $c->id,
                'name'           => $c->name,
                'slug'           => $c->slug,
                'icon'           => $c->icon ?? 'fa-solid fa-bag-shopping',
                'image_url'      => $c->image ? asset('storage/' . $c->image) : asset('img/saksershop-logo.png'),
                'products_count' => $c->products_count,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ]);
    }
}
