@forelse($categories as $category)
    <tr id="row-{{ $category->id }}">
        <td class="font-weight-bold">{{ $category->id }}</td>
        <td>{{ $category->title }}</td>
        <td>
            @if($category->image)
                <img src="{{ asset('upload/' . $category->image) }}" alt="image"
                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
            @else
                No Image
            @endif
        </td>
        <td>
            @if($category->status)
                <span class="badge-active status-toggle" data-id="{{ $category->id }}">Active</span>
            @else
                <span class="badge-inactive status-toggle" data-id="{{ $category->id }}">Inactive</span>
            @endif
        </td>
        <td>
            <a href="{{ route('categories.edit', $category->id) }}" class="btn-edit-custom me-1">Edit</a>
            <button class="btn-delete-custom delete-btn" data-id="{{ $category->id }}">Delete</button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-4 text-muted">No categories found.</td>
    </tr>
@endforelse
