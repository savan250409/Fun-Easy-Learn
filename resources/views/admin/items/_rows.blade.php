@forelse($items as $item)
    <tr id="row-{{ $item->id }}">
        <td class="font-weight-bold">{{ $item->id }}</td>
        <td>{{ $item->title }}</td>
        <td class="text-muted" style="font-size:0.8rem;">
            @if($item->childCategory)
                {{ $item->childCategory->subCategory->title ?? 'N/A' }} ❭
                <strong>{{ $item->childCategory->title ?? 'N/A' }}</strong>
            @elseif($item->subCategory)
                {{ $item->subCategory->category->title ?? 'N/A' }} ❭
                <strong>{{ $item->subCategory->title ?? 'N/A' }}</strong>
            @else
                N/A
            @endif
        </td>
        <td>
            @if($item->image)
                <img src="{{ asset('upload/' . $item->image) }}" alt="Image"
                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
            @else
                No Image
            @endif
        </td>
        <td>
            @if($item->status)
                <span class="badge-active status-toggle" data-id="{{ $item->id }}">Active</span>
            @else
                <span class="badge-inactive status-toggle" data-id="{{ $item->id }}">Inactive</span>
            @endif
        </td>
        <td>
            <a href="{{ route('items.edit', $item->id) }}" class="btn-edit-custom me-1">Edit</a>
            <button class="btn-delete-custom delete-btn" data-id="{{ $item->id }}">Delete</button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-4 text-muted">No items found.</td>
    </tr>
@endforelse
