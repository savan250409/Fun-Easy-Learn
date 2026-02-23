<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    public function getAllCategories()
    {
        $categories = Category::where('status', 1)->get();

        $data = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'key' => $category->key,
                'title' => $category->title,
                'image_url' => $category->image ? str_replace(' ', '%20', $category->image) : null,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Categories fetched successfully',
            'data' => $data
        ]);
    }

    public function getCategoryData(Request $request)
    {
        $categoryId = $request->category_id;

        if (!$categoryId) {
            return response()->json([
                'status' => false,
                'message' => 'category_id is required',
            ], 400);
        }

        $category = Category::with([
            'subCategories' => function ($query) {
                $query->where('status', 1);
            },
            'subCategories.childCategories' => function ($query) {
                $query->where('status', 1);
            },
            'subCategories.childCategories.items' => function ($query) {
                $query->where('status', 1);
            }
        ])->where('id', $categoryId)->where('status', 1)->first();

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found or inactive',
            ], 404);
        }

        $data = [
            'id' => $category->id,
            'key' => $category->key,
            'title' => $category->title,
            'image_url' => $category->image ? str_replace(' ', '%20', $category->image) : null,
            'sub_categories' => $category->subCategories->map(function ($subCategory) {

                $children = $subCategory->childCategories->map(function ($childCategory) {
                    return [
                        'id' => $childCategory->id,
                        'key' => $childCategory->key,
                        'title' => $childCategory->title,
                        'image_url' => $childCategory->image ? str_replace(' ', '%20', $childCategory->image) : null,
                        'total_item' => $childCategory->items->count(),
                        'items' => $childCategory->items->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'title' => $item->title,
                                'image_url' => $item->image ? str_replace(' ', '%20', $item->image) : null,
                            ];
                        })->values()
                    ];
                })->values();

                $total_item = $subCategory->childCategories->sum(function ($childCategory) {
                    return $childCategory->items->count();
                });

                return [
                    'id' => $subCategory->id,
                    'key' => $subCategory->key,
                    'title' => $subCategory->title,
                    'image_url' => $subCategory->image ? str_replace(' ', '%20', $subCategory->image) : null,
                    'total_item' => $total_item,
                    'children' => $children
                ];
            })->values()
        ];

        return response()->json([
            'status' => true,
            'message' => 'Category data fetched successfully',
            'data' => $data
        ]);
    }
}
