@extends('layouts.app')

@section('content')
    <div class="page-header-custom">
        <div>
            <h3 class="page-title-custom">
                <i class="mdi mdi-folder-outline page-title-icon-custom"></i> Subcategories Management
            </h3>
            <p class="page-subtitle-custom">Manage subcategories</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="stats-badge me-3">
                <i class="mdi mdi-layers"></i> Total: {{ $subcategories->total() }} Subcategories
            </div>
            <a href="{{ route('subcategories.create') }}" class="btn-purple-custom">
                + Add Subcategory
            </a>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-body">
            <div class="custom-search-wrapper">
                <div class="d-flex align-items-center">
                    <span style="font-size:0.85rem; color:#6c757d; margin-right:5px;">Show</span>
                    <select class="entries-select-box">
                        <option>10</option>
                    </select>
                    <span style="font-size:0.85rem; color:#6c757d; margin-left:5px;">entries</span>
                </div>

                <form action="{{ route('subcategories.index') }}" method="GET" class="d-flex align-items-center">
                    <select name="category_id" class="search-input-box me-2" style="background:#fff;">
                        <option value="">Filter by Category...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (isset($categoryId) && $categoryId == $cat->id) ? 'selected' : '' }}>{{ $cat->title }}</option>
                        @endforeach
                    </select>
                    <span style="font-size:0.85rem; color:#6c757d; margin-right:10px;">Search:</span>
                    <input type="text" name="search" class="search-input-box" placeholder="Search subcategories..."
                        value="{{ $search ?? '' }}">
                    <button type="submit" style="display:none;"></button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Subcategory Name</th>
                            <th>Parent Category</th>
                            <th>Subcategory Image</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subcategories as $subcategory)
                            <tr id="row-{{ $subcategory->id }}">
                                <td class="font-weight-bold">{{ $subcategory->id }}</td>
                                <td>{{ $subcategory->title }}</td>
                                <td class="text-muted">{{ $subcategory->category->title ?? 'N/A' }}</td>
                                <td>
                                    @if($subcategory->image)
                                        <img src="{{ asset('uploads/subcategories/' . $subcategory->image) }}" alt="image"
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
                                    <a href="{{ route('subcategories.edit', $subcategory->id) }}"
                                        class="btn-edit-custom me-1">Edit</a>
                                    <button class="btn-delete-custom delete-btn"
                                        data-id="{{ $subcategory->id }}">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No subcategories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top"
                style="font-size:0.85rem; color:#6c757d;">
                <div>Showing {{ $subcategories->firstItem() ?? 0 }} to {{ $subcategories->lastItem() ?? 0 }} of
                    {{ $subcategories->total() }} entries
                </div>
                <div>{{ $subcategories->withQueryString()->links('pagination::bootstrap-4') }}</div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ff7b94',
                        cancelButtonColor: '#e2e6ea',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`{{ url('admin/subcategories') }}/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Deleted!',
                                            text: data.message,
                                            timer: 5000,
                                            timerProgressBar: true,
                                            showConfirmButton: false
                                        });
                                        document.getElementById(`row-${id}`).remove();
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Cannot Delete!',
                                            text: data.message,
                                            timer: 5000,
                                            timerProgressBar: true,
                                            showConfirmButton: false
                                        });
                                    }
                                })
                                .catch(error => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Something went wrong.',
                                        timer: 5000,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    });
                                });
                        }
                    })
                });
            });

            const statusToggles = document.querySelectorAll('.status-toggle');
            statusToggles.forEach(toggle => {
                toggle.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const badge = this;

                    fetch(`{{ url('admin/subcategories') }}/${id}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                if (data.new_status) {
                                    badge.textContent = 'Active';
                                    badge.className = 'badge-active status-toggle';
                                } else {
                                    badge.textContent = 'Inactive';
                                    badge.className = 'badge-inactive status-toggle';
                                }
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Updated!',
                                    text: data.message,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong.',
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                        });
                });
            });
        });
    </script>
@endsection