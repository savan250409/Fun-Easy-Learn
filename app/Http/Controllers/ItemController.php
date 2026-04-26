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

        $perPage = in_array((int) $request->input('per_page'), [5, 10, 50, 100]) ? (int) $request->input('per_page') : 10;

        $items = Item::with(['childCategory.subCategory.category', 'subCategory.category'])
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%");
            })
            ->when($childCategoryId, function ($query, $childCategoryId) {
                return $query->where('child_category_id', $childCategoryId);
            })
            ->latest()
            ->paginate($perPage);

        $childCategories = ChildCategory::with('subCategory.category')->get();

        if ($request->ajax()) {
            return response()->json([
                'rows_html'       => view('admin.items._rows', compact('items'))->render(),
                'pagination_html' => $items->links('pagination::bootstrap-4')->render(),
                'from'            => $items->firstItem() ?? 0,
                'to'              => $items->lastItem() ?? 0,
                'total'           => $items->total(),
            ]);
        }

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
            'sub_category_id' => 'required|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'status' => 'boolean',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($request->filled('child_category_id')) {
                $childCategory = ChildCategory::findOrFail($request->child_category_id);
                $categoryTitle = $childCategory->subCategory->category->title;
            } else {
                $subCategory = SubCategory::findOrFail($request->sub_category_id);
                $categoryTitle = $subCategory->category->title;
            }
            $imageName = $request->image->getClientOriginalName();
            $relativePath = $categoryTitle . '/item image/' . $imageName;
            $request->image->move(public_path('upload/' . $categoryTitle . '/item image'), $imageName);
            $data['image'] = $relativePath;
        }

        Item::create($data);

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        $selectedSubCategoryId = $item->sub_category_id ?? ($item->childCategory->sub_category_id ?? null);
        $selectedCategoryId = null;

        if ($item->subCategory) {
            $selectedCategoryId = $item->subCategory->category_id;
        } elseif ($item->childCategory) {
            $selectedCategoryId = $item->childCategory->subCategory->category_id;
        }

        $subCategories = SubCategory::where('category_id', $selectedCategoryId)->get();
        $childCategories = ChildCategory::where('sub_category_id', $selectedSubCategoryId)->get();

        return view('admin.items.form', compact('item', 'categories', 'subCategories', 'childCategories', 'selectedCategoryId', 'selectedSubCategoryId'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'status' => 'boolean',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($item->image && file_exists(public_path('upload/' . $item->image))) {
                unlink(public_path('upload/' . $item->image));
            }
            if ($request->filled('child_category_id')) {
                $childCategory = ChildCategory::findOrFail($request->child_category_id);
                $categoryTitle = $childCategory->subCategory->category->title;
            } else {
                $subCategory = SubCategory::findOrFail($request->sub_category_id);
                $categoryTitle = $subCategory->category->title;
            }
            $imageName = $request->image->getClientOriginalName();
            $relativePath = $categoryTitle . '/item image/' . $imageName;
            $request->image->move(public_path('upload/' . $categoryTitle . '/item image'), $imageName);
            $data['image'] = $relativePath;
        }

        $item->update($data);

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        if ($item->image && file_exists(public_path('upload/' . $item->image))) {
            unlink(public_path('upload/' . $item->image));
        }

        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item deleted successfully.'
        ]);
    }
    public function getChildCategories($subCategoryId)
    {
        $childCategories = ChildCategory::where('sub_category_id', $subCategoryId)->where('status', 1)->get();
        return response()->json($childCategories);
    }

    public function toggleStatus($id)
    {
        $item = Item::findOrFail($id);
        $item->status = !$item->status;
        $item->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Item status updated successfully.',
            'new_status' => $item->status
        ]);
    }
}
