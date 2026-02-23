<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    public function getCategories(Request $request)
    {
        // Fetch categories with all nested relationships, filtering by active status where applicable
        $categories = Category::with([
            'subCategories' => function ($query) {
                $query->where('status', 1);
            },
            'subCategories.childCategories' => function ($query) {
                $query->where('status', 1);
            },
            'subCategories.childCategories.items' => function ($query) {
                $query->where('status', 1);
            }
        ])->where('status', 1)->get();

        $data = $categories->map(function ($category) {
            return [
                'id' => 'cat_' . $category->id,
                'key' => $category->key,
                'title' => $category->title,
                'image_url' => $category->image ? url('uploads/categories/' . $category->image) : null,
                'sub_categories' => $category->subCategories->map(function ($subCategory) {

                    $children = $subCategory->childCategories->map(function ($childCategory) {
                        return [
                            'id' => 'child_' . $childCategory->id,
                            'key' => $childCategory->key,
                            'title' => $childCategory->title,
                            'image_url' => $childCategory->image ? url('uploads/childcategories/' . $childCategory->image) : null,
                            'total_item' => $childCategory->items->count(),
                            'items' => $childCategory->items->map(function ($item) {
                                return [
                                    'id' => 'item_' . $item->id,
                                    'title' => $item->title,
                                    'image_url' => $item->image ? url('uploads/items/' . $item->image) : null,
                                ];
                            })->values()
                        ];
                    })->values();

                    $total_item = $subCategory->childCategories->sum(function ($childCategory) {
                        return $childCategory->items->count();
                    });

                    $output = [
                        'id' => 'sub_' . $subCategory->id,
                        'key' => $subCategory->key,
                        'title' => $subCategory->title,
                        'image_url' => $subCategory->image ? url('uploads/subcategories/' . $subCategory->image) : null,
                        'total_item' => $total_item,
                    ];

                    // Note: If you have a specific rule where empty "children" should collapse to just "items",
                    // you can do logic here. Otherwise, the pure 4-level structure exposes children.
                    $output['children'] = $children;

                    return $output;
                })->values()
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Data fetched successfully',
            'data' => $data
        ]);
    }
}
