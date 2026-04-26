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
        $perPage = in_array((int) $request->input('per_page'), [5, 10, 50, 100]) ? (int) $request->input('per_page') : 10;

        $categories = Category::when($search, function ($query, $search) {
            return $query->where('title', 'like', "%{$search}%")
                ->orWhere('key', 'like', "%{$search}%");
        })->latest()->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'rows_html'       => view('admin.categories._rows', compact('categories'))->render(),
                'pagination_html' => $categories->links('pagination::bootstrap-4')->render(),
                'from'            => $categories->firstItem() ?? 0,
                'to'              => $categories->lastItem() ?? 0,
                'total'           => $categories->total(),
            ]);
        }

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
            $categoryTitle = $request->title;
            $imageName = $request->image->getClientOriginalName();
            $relativePath = $categoryTitle . '/category image/' . $imageName;
            $request->image->move(public_path('upload/' . $categoryTitle . '/category image'), $imageName);
            $data['image'] = $relativePath;
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
            if ($category->image && file_exists(public_path('upload/' . $category->image))) {
                unlink(public_path('upload/' . $category->image));
            }
            $categoryTitle = $request->title;
            $imageName = $request->image->getClientOriginalName();
            $relativePath = $categoryTitle . '/category image/' . $imageName;
            $request->image->move(public_path('upload/' . $categoryTitle . '/category image'), $imageName);
            $data['image'] = $relativePath;
        }

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if (file_exists(public_path('upload/' . $category->title))) {
            $deleted = \Illuminate\Support\Facades\File::deleteDirectory(public_path('upload/' . $category->title));
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
