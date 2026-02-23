<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $childCategoryId = $request->input('child_category_id');

        $items = Item::with('childCategory.subCategory.category')
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%");
            })
            ->when($childCategoryId, function ($query, $childCategoryId) {
                return $query->where('child_category_id', $childCategoryId);
            })
            ->latest()
            ->paginate(10);

        $childCategories = ChildCategory::with('subCategory.category')->get();

        return view('admin.items.index', compact('items', 'search', 'childCategories', 'childCategoryId'));
    }

    public function create()
    {
        $categories = Category::all();
        $subCategories = [];
        $childCategories = [];
        return view('admin.items.form', compact('categories', 'subCategories', 'childCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'child_category_id' => 'required|exists:child_categories,id',
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'status' => 'boolean',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            $imageName = $request->image->getClientOriginalName();
            $request->image->move(public_path('uploads/items'), $imageName);
            $data['image'] = $imageName;
        }

        Item::create($data);

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        $selectedSubCategoryId = $item->childCategory->sub_category_id ?? null;
        $selectedCategoryId = $item->childCategory->subCategory->category_id ?? null;

        $subCategories = SubCategory::where('category_id', $selectedCategoryId)->get();
        $childCategories = ChildCategory::where('sub_category_id', $selectedSubCategoryId)->get();

        return view('admin.items.form', compact('item', 'categories', 'subCategories', 'childCategories', 'selectedCategoryId', 'selectedSubCategoryId'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'child_category_id' => 'required|exists:child_categories,id',
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'status' => 'boolean',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($item->image && file_exists(public_path('uploads/items/' . basename($item->image)))) {
                unlink(public_path('uploads/items/' . basename($item->image)));
            }
            $imageName = $request->image->getClientOriginalName();
            $request->image->move(public_path('uploads/items'), $imageName);
            $data['image'] = $imageName;
        }

        $item->update($data);

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        if ($item->image && file_exists(public_path('uploads/items/' . basename($item->image)))) {
            unlink(public_path('uploads/items/' . basename($item->image)));
        }

        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item deleted successfully.'
        ]);
    }
    public function getChildCategories($subCategoryId)
    {
        $childCategories = ChildCategory::where('sub_category_id', $subCategoryId)->get();
        return response()->json($childCategories);
    }
}
