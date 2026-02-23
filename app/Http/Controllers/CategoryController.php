<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categories = Category::when($search, function ($query, $search) {
            return $query->where('title', 'like', "%{$search}%")
                ->orWhere('key', 'like', "%{$search}%");
        })->latest()->paginate(10);

        return view('admin.categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        return view('admin.categories.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:categories,key',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'status' => 'boolean',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            $imageName = $request->image->getClientOriginalName();
            $request->image->move(public_path('uploads/categories'), $imageName);
            $data['image'] = $imageName;
        }

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:categories,key,' . $category->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'status' => 'boolean',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($category->image && file_exists(public_path('uploads/categories/' . basename($category->image)))) {
                unlink(public_path('uploads/categories/' . basename($category->image)));
            }
            $imageName = $request->image->getClientOriginalName();
            $request->image->move(public_path('uploads/categories'), $imageName);
            $data['image'] = $imageName;
        }

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->subcategories()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete Category because it has SubCategories associated with it.'
            ], 403);
        }

        if ($category->image && file_exists(public_path('uploads/categories/' . basename($category->image)))) {
            unlink(public_path('uploads/categories/' . basename($category->image)));
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully.'
        ]);
    }

    public function toggleStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->status = !$category->status;
        $category->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Category status updated successfully.',
            'new_status' => $category->status
        ]);
    }
}
