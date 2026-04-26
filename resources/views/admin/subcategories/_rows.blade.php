@forelse($subcategories as $subcategory)
    <tr id="row-{{ $subcategory->id }}">
        <td class="font-weight-bold">{{ $subcategory->id }}</td>
        <td>{{ $subcategory->title }}</td>
        <td class="text-muted">{{ $subcategory->category->title ?? 'N/A' }}</td>
        <td>
            @if($subcategory->image)
                <img src="{{ asset('upload/' . $subcategory->image) }}" alt="image"
                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
            @else
                No Image
            @endif
        </td>
        <td>
            @if($subcategory->status)
                <span class="badge-active status-toggle" data-id="{{ $subcategory->id }}">Active</span>
            @else
                <span class="badge-inactive status-toggle" data-id="{{ $subcategory->id }}">Inactive</span>
            @endif
        </td>
        <td>
            <a href="{{ route('subcategories.edit', $subcategory->id) }}" class="btn-edit-custom me-1">Edit</a>
            <button class="btn-delete-custom delete-btn" data-id="{{ $subcategory->id }}">Delete</button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-4 text-muted">No subcategories found.</td>
    </tr>
@endforelse
