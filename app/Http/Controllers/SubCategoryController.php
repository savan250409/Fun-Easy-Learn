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

        $perPage = in_array((int) $request->input('per_page'), [5, 10, 50, 100]) ? (int) $request->input('per_page') : 10;

        $subcategories = SubCategory::with('category')
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('key', 'like', "%{$search}%");
            })
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->latest()
            ->paginate($perPage);

        $categories = Category::all();

        if ($request->ajax()) {
            return response()->json([
                'rows_html'       => view('admin.subcategories._rows', compact('subcategories'))->render(),
                'pagination_html' => $subcategories->links('pagination::bootstrap-4')->render(),
                'from'            => $subcategories->firstItem() ?? 0,
                'to'              => $subcategories->lastItem() ?? 0,
                'total'           => $subcategories->total(),
            ]);
        }

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
            $category = Category::findOrFail($request->category_id);
            $categoryTitle = $category->title;
            $imageName = $request->image->getClientOriginalName();
            $relativePath = $categoryTitle . '/sub category image/' . $imageName;
            $request->image->move(public_path('upload/' . $categoryTitle . '/sub category image'), $imageName);
            $data['image'] = $relativePath;
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
            if ($subcategory->image && file_exists(public_path('upload/' . $subcategory->image))) {
                unlink(public_path('upload/' . $subcategory->image));
            }
            $category = Category::findOrFail($request->category_id);
            $categoryTitle = $category->title;
            $imageName = $request->image->getClientOriginalName();
            $relativePath = $categoryTitle . '/sub category image/' . $imageName;
            $request->image->move(public_path('upload/' . $categoryTitle . '/sub category image'), $imageName);
            $data['image'] = $relativePath;
        }

        $subcategory->update($data);

        return redirect()->route('subcategories.index')->with('success', 'SubCategory updated successfully.');
    }

    public function destroy(SubCategory $subcategory)
    {
        // Clean up related Item images
        $items = \App\Models\Item::where('sub_category_id', $subcategory->id)->get();
        foreach ($items as $item) {
            $this->deleteImageAndCleanupFolder($item->image);
        }

        // Clean up related Child Category images
        foreach ($subcategory->childCategories as $childCategory) {
            $this->deleteImageAndCleanupFolder($childCategory->image);
        }

        // Clean up SubCategory image itself
        $this->deleteImageAndCleanupFolder($subcategory->image);

        $subcategory->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'SubCategory deleted successfully.'
        ]);
    }

    private function deleteImageAndCleanupFolder($relativePath)
    {
        if (!$relativePath)
            return;

        $fullPath = public_path('upload/' . $relativePath);
        if (file_exists($fullPath) && is_file($fullPath)) {
            @unlink($fullPath);

            // Check if the specific image folder (e.g., 'sub category image') is now empty
            $specificFolder = dirname($fullPath);
            if (is_dir($specificFolder) && count(scandir($specificFolder)) === 2) { // practically empty (only . and .. remain)
                @rmdir($specificFolder);
            }

            // Check if the root Category folder is now empty
            $categoryFolder = dirname($specificFolder);
            if (is_dir($categoryFolder) && count(scandir($categoryFolder)) === 2) {
                @rmdir($categoryFolder);
            }
        }
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
