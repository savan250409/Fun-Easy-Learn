<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $subcategories = SubCategory::with('category')
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('key', 'like', "%{$search}%");
            })
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->latest()
            ->paginate(10);

        $categories = Category::all();

        return view('admin.subcategories.index', compact('subcategories', 'search', 'categories', 'categoryId'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.subcategories.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:sub_categories,key',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'status' => 'boolean',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            $imageName = $request->image->getClientOriginalName();
            $request->image->move(public_path('uploads/subcategories'), $imageName);
            $data['image'] = $imageName;
        }

        SubCategory::create($data);

        return redirect()->route('subcategories.index')->with('success', 'SubCategory created successfully.');
    }

    public function edit(SubCategory $subcategory)
    {
        $categories = Category::all();
        return view('admin.subcategories.form', compact('subcategory', 'categories'));
    }

    public function update(Request $request, SubCategory $subcategory)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:sub_categories,key,' . $subcategory->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'status' => 'boolean',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($subcategory->image && file_exists(public_path('uploads/subcategories/' . basename($subcategory->image)))) {
                unlink(public_path('uploads/subcategories/' . basename($subcategory->image)));
            }
            $imageName = $request->image->getClientOriginalName();
            $request->image->move(public_path('uploads/subcategories'), $imageName);
            $data['image'] = $imageName;
        }

        $subcategory->update($data);

        return redirect()->route('subcategories.index')->with('success', 'SubCategory updated successfully.');
    }

    public function destroy(SubCategory $subcategory)
    {
        if ($subcategory->childCategories()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete SubCategory because it has Child Categories associated with it.'
            ], 403);
        }

        if ($subcategory->image && file_exists(public_path('uploads/subcategories/' . basename($subcategory->image)))) {
            unlink(public_path('uploads/subcategories/' . basename($subcategory->image)));
        }

        $subcategory->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'SubCategory deleted successfully.'
        ]);
    }

    public function toggleStatus($id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $subcategory->status = !$subcategory->status;
        $subcategory->save();

        return response()->json([
            'status' => 'success',
            'message' => 'SubCategory status updated successfully.',
            'new_status' => $subcategory->status
        ]);
    }
}
