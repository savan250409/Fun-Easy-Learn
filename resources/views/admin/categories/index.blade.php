@extends('layouts.app')

@section('content')
    <div class="page-header-custom">
        <div>
            <h3 class="page-title-custom">
                <i class="mdi mdi-image page-title-icon-custom"></i> Category Management
            </h3>
            <p class="page-subtitle-custom">Manage categories</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="stats-badge me-3">
                <i class="mdi mdi-layers"></i> Total: {{ $categories->total() }} Categories
            </div>
            <a href="{{ route('categories.create') }}" class="btn-purple-custom">
                + Add Category
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

                <form action="{{ route('categories.index') }}" method="GET" class="d-flex align-items-center">
                    <span style="font-size:0.85rem; color:#6c757d; margin-right:10px;">Search:</span>
                    <input type="text" name="search" class="search-input-box" placeholder="Search categories..."
                        value="{{ $search ?? '' }}">
                </form>
            </div>

            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th>Category Image</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr id="row-{{ $category->id }}">
                                <td class="font-weight-bold">{{ $category->id }}</td>
                                <td>{{ $category->title }}</td>
                                <td>
                                    @if($category->image)
                                        <img src="{{ asset('uploads/categories/' . $category->image) }}" alt="image"
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
                                    <a href="{{ route('categories.edit', $category->id) }}"
                                        class="btn-edit-custom me-1">Edit</a>
                                    <button class="btn-delete-custom delete-btn" data-id="{{ $category->id }}">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top"
                style="font-size:0.85rem; color:#6c757d;">
                <div>Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of
                    {{ $categories->total() }} entries
                </div>
                <div>{{ $categories->withQueryString()->links('pagination::bootstrap-4') }}</div>
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
                            fetch(`{{ url('admin/categories') }}/${id}`, {
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

                    fetch(`{{ url('admin/categories') }}/${id}/toggle-status`, {
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