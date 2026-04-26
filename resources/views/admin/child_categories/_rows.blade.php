@forelse($childCategories as $childCategory)
    <tr id="row-{{ $childCategory->id }}">
        <td class="font-weight-bold">{{ $childCategory->id }}</td>
        <td>{{ $childCategory->title }}</td>
        <td class="text-muted" style="font-size:0.8rem;">
            {{ $childCategory->subCategory->category->title ?? 'N/A' }} ❭
            <strong>{{ $childCategory->subCategory->title ?? 'N/A' }}</strong>
        </td>
        <td>
            @if($childCategory->image)
                <img src="{{ asset('upload/' . $childCategory->image) }}" alt="image"
                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
            @else
                No Image
            @endif
        </td>
        <td>
            @if($childCategory->status)
                <span class="badge-active status-toggle" data-id="{{ $childCategory->id }}">Active</span>
            @else
                <span class="badge-inactive status-toggle" data-id="{{ $childCategory->id }}">Inactive</span>
            @endif
        </td>
        <td>
            <a href="{{ route('child-categories.edit', $childCategory->id) }}" class="btn-edit-custom me-1">Edit</a>
            <button class="btn-delete-custom delete-btn" data-id="{{ $childCategory->id }}">Delete</button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-4 text-muted">No child categories found.</td>
    </tr>
@endforelse
