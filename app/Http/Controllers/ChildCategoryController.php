<?php

namespace App\Http\Controllers;

use App\Models\ChildCategory;
use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;

class ChildCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $subCategoryId = $request->input('sub_category_id');

        $childCategories = ChildCategory::with('subCategory.category')
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('key', 'like', "%{$search}%");
            })
            ->when($subCategoryId, function ($query, $subCategoryId) {
                return $query->where('sub_category_id', $subCategoryId);
            })
            ->latest()
            ->paginate(10);

        $subCategories = SubCategory::with('category')->get();

        return view('admin.child_categories.index', compact('childCategories', 'search', 'subCategories', 'subCategoryId'));
    }

    public function create()
    {
        $categories = Category::all();
        $subCategories = []; // Initially empty, populated via AJAX
        return view('admin.child_categories.form', compact('categories', 'subCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:sub_categories,id',
            'title' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:child_categories,key',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'status' => 'boolean',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            $subCategory = SubCategory::findOrFail($request->sub_category_id);
            $categoryTitle = $subCategory->category->title;
            $imageName = $request->image->getClientOriginalName();
            $relativePath = $categoryTitle . '/chield category image/' . $imageName;
            $request->image->move(public_path('upload/' . $categoryTitle . '/chield category image'), $imageName);
            $data['image'] = $relativePath;
        }

        ChildCategory::create($data);

        return redirect()->route('child-categories.index')->with('success', 'Child Category created successfully.');
    }

    public function edit(ChildCategory $childCategory)
    {
        $categories = Category::all();
        $selectedCategoryId = $childCategory->subCategory->category_id ?? null;
        $subCategories = SubCategory::where('category_id', $selectedCategoryId)->get();

        return view('admin.child_categories.form', compact('childCategory', 'categories', 'subCategories', 'selectedCategoryId'));
    }

    public function update(Request $request, ChildCategory $childCategory)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:sub_categories,id',
            'title' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:child_categories,key,' . $childCategory->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'status' => 'boolean',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($childCategory->image && file_exists(public_path('upload/' . $childCategory->image))) {
                unlink(public_path('upload/' . $childCategory->image));
            }
            $subCategory = SubCategory::findOrFail($request->sub_category_id);
            $categoryTitle = $subCategory->category->title;
            $imageName = $request->image->getClientOriginalName();
            $relativePath = $categoryTitle . '/chield category image/' . $imageName;
            $request->image->move(public_path('upload/' . $categoryTitle . '/chield category image'), $imageName);
            $data['image'] = $relativePath;
        }

        $childCategory->update($data);

        return redirect()->route('child-categories.index')->with('success', 'Child Category updated successfully.');
    }

    public function destroy(ChildCategory $childCategory)
    {
        if ($childCategory->items()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete Child Category because it has Items associated with it.'
            ], 403);
        }

        if ($childCategory->image && file_exists(public_path('upload/' . $childCategory->image))) {
            unlink(public_path('upload/' . $childCategory->image));
        }

        $childCategory->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Child Category deleted successfully.'
        ]);
    }
    public function getSubcategories($categoryId)
    {
        $subcategories = SubCategory::where('category_id', $categoryId)->where('status', 1)->get();
        return response()->json($subcategories);
    }

    public function toggleStatus($id)
    {
        $childCategory = ChildCategory::findOrFail($id);
        $childCategory->status = !$childCategory->status;
        $childCategory->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Child Category status updated successfully.',
            'new_status' => $childCategory->status
        ]);
    }
}
