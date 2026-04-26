@extends('layouts.app')

@section('content')
    <div class="page-header-custom">
        <div>
            <h3 class="page-title-custom">
                <i class="mdi mdi-view-list page-title-icon-custom"></i> Items Management
            </h3>
            <p class="page-subtitle-custom">Manage items</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="stats-badge me-3">
                <i class="mdi mdi-layers"></i> Total: <span id="totalCount">{{ $items->total() }}</span> Items
            </div>
            <a href="{{ route('items.create') }}" class="btn-purple-custom">
                + Add Item
            </a>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-body">
            <div class="custom-search-wrapper">
                <div class="d-flex align-items-center">
                    <span style="font-size:0.85rem; color:#6c757d; margin-right:5px;">Show</span>
                    <select class="entries-select-box" id="entriesSelect" onchange="changeEntries(this.value)">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span style="font-size:0.85rem; color:#6c757d; margin-left:5px;">entries</span>
                </div>

                <div class="d-flex align-items-center">
                    <select id="filterSelect" class="search-input-box me-2" style="background:#fff;">
                        <option value="">Filter by Child Category...</option>
                        @foreach($childCategories as $childCat)
                            <option value="{{ $childCat->id }}">
                                {{ $childCat->subCategory->category->title ?? 'N/A' }} ❭ {{ $childCat->title }}
                            </option>
                        @endforeach
                    </select>
                    <span style="font-size:0.85rem; color:#6c757d; margin-right:10px;">Search:</span>
                    <input type="text" id="searchInput" class="search-input-box" placeholder="Search items...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Hierarchy</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @include('admin.items._rows')
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top"
                style="font-size:0.85rem; color:#6c757d;">
                <div id="showingText">Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} entries</div>
                <div id="paginationWrapper">{{ $items->links('pagination::bootstrap-4') }}</div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    let currentSearch  = '';
    let currentFilter  = '';
    let currentPerPage = 10;
    let currentPage    = 1;
    let searchTimer;

    function changeEntries(val) {
        currentPerPage = parseInt(val);
        currentPage    = 1;
        fetchData();
    }

    function fetchData() {
        const params = new URLSearchParams({
            search:            currentSearch,
            child_category_id: currentFilter,
            per_page:          currentPerPage,
            page:              currentPage,
        });

        fetch('{{ route('items.index') }}?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('tableBody').innerHTML        = data.rows_html;
            document.getElementById('paginationWrapper').innerHTML = data.pagination_html;
            document.getElementById('showingText').textContent    = 'Showing ' + data.from + ' to ' + data.to + ' of ' + data.total + ' entries';
            document.getElementById('totalCount').textContent     = data.total;
        });
    }

    document.addEventListener('DOMContentLoaded', function () {

        document.getElementById('searchInput').addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                currentSearch = this.value;
                currentPage   = 1;
                fetchData();
            }, 400);
        });

        document.getElementById('filterSelect').addEventListener('change', function () {
            currentFilter = this.value;
            currentPage   = 1;
            fetchData();
        });

        document.getElementById('paginationWrapper').addEventListener('click', function (e) {
            const link = e.target.closest('a[href]');
            if (!link) return;
            e.preventDefault();
            try {
                currentPage = parseInt(new URL(link.href).searchParams.get('page')) || 1;
            } catch (_) { currentPage = 1; }
            fetchData();
        });

        document.getElementById('tableBody').addEventListener('click', function (e) {
            const btn = e.target.closest('.delete-btn');
            if (!btn) return;
            const id = btn.getAttribute('data-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff7b94',
                cancelButtonColor: '#e2e6ea',
                confirmButtonText: 'Yes, delete it!'
            }).then(result => {
                if (!result.isConfirmed) return;
                fetch(`{{ url('admin/items') }}/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Deleted!', text: data.message, timer: 3000, timerProgressBar: true, showConfirmButton: false });
                        fetchData();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Cannot Delete!', text: data.message, timer: 5000, timerProgressBar: true, showConfirmButton: false });
                    }
                })
                .catch(() => Swal.fire({ icon: 'error', title: 'Error!', text: 'Something went wrong.', timer: 5000, timerProgressBar: true, showConfirmButton: false }));
            });
        });

        document.getElementById('tableBody').addEventListener('click', function (e) {
            const toggle = e.target.closest('.status-toggle');
            if (!toggle) return;
            const id = toggle.getAttribute('data-id');

            fetch(`{{ url('admin/items') }}/${id}/toggle-status`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    toggle.textContent = data.new_status ? 'Active' : 'Inactive';
                    toggle.className   = data.new_status ? 'badge-active status-toggle' : 'badge-inactive status-toggle';
                    Swal.fire({ icon: 'success', title: 'Updated!', text: data.message, timer: 3000, timerProgressBar: true, showConfirmButton: false });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error!', text: 'Something went wrong.', timer: 3000, timerProgressBar: true, showConfirmButton: false }));
        });
    });
</script>
@endsection
