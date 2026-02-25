@extends('layouts.app')

@section('content')
    <div class="page-header-custom" style="margin-bottom: 1.5rem; border-bottom: none; padding-bottom: 0;">
        <div>
            <h3 class="page-title-custom">
                <i class="mdi mdi-view-list page-title-icon-custom"></i> {{ isset($item) ? 'Edit Item' : 'Add New Item' }}
            </h3>
            <p class="page-subtitle-custom">{{ isset($item) ? 'Modify an existing item' : 'Create a new Item' }}</p>
        </div>
        <div>
            <a href="{{ route('items.index') }}" class="btn-back-custom">
                <i class="mdi mdi-arrow-left"></i> Back to Items
            </a>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-body" style="padding: 2rem;">
            <form action="{{ isset($item) ? route('items.update', $item->id) : route('items.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @if(isset($item))
                    @method('PUT')
                @endif

                <div class="mb-4">
                    <label for="category_id" class="form-label-custom d-block">Category</label>
                    <select class="form-control-custom" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (old('category_id', $selectedCategoryId ?? '') == $cat->id) ? 'selected' : '' }}>
                                {{ $cat->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="sub_category_id" class="form-label-custom d-block">Subcategory</label>
                    <select class="form-control-custom" id="sub_category_id" name="sub_category_id" required>
                        <option value="">Select Subcategory</option>
                        @foreach($subCategories as $subCat)
                            <option value="{{ $subCat->id }}" {{ (old('sub_category_id', $selectedSubCategoryId ?? '') == $subCat->id) ? 'selected' : '' }}>
                                {{ $subCat->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="child_category_id" class="form-label-custom d-block">Parent Child Category</label>
                    <select class="form-control-custom @error('child_category_id') is-invalid @enderror"
                        id="child_category_id" name="child_category_id">
                        <option value="">Select Child Category</option>
                        @foreach($childCategories as $childCat)
                            <option value="{{ $childCat->id }}" {{ old('child_category_id', $item->child_category_id ?? '') == $childCat->id ? 'selected' : '' }}>
                                {{ $childCat->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('child_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label for="title" class="form-label-custom d-block">Item Name</label>
                    <input type="text" class="form-control-custom @error('title') is-invalid @enderror" id="title"
                        name="title" value="{{ old('title', $item->title ?? '') }}" placeholder="Item Name" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label-custom d-block">Status</label>
                    <label class="custom-checkbox-label">
                        <input type="checkbox" name="status" value="1" {{ old('status', $item->status ?? 1) ? 'checked' : '' }}>
                        Active
                    </label>
                </div>

                <div class="mb-5">
                    <label for="image" class="form-label-custom d-block">Item Image</label>
                    <div class="file-upload-wrapper">
                        <label class="file-upload-btn" for="image">Choose Image</label>
                        <div class="file-upload-text" id="fileName">
                            {{ isset($item) && $item->image ? '1 file attached' : 'No file chosen' }}
                        </div>
                        <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                    </div>
                    @error('image')<div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>@enderror

                    <div class="mt-3">
                        <img id="imagePreview"
                            src="{{ isset($item) && $item->image ? asset('upload/' . $item->image) : '#' }}"
                            alt="Image Preview"
                            style="display: {{ isset($item) && $item->image ? 'block' : 'none' }}; max-width: 150px; border-radius: 4px; border: 1px solid #eaeaea; padding: 3px;">
                    </div>
                </div>

                <div class="d-flex mt-4">
                    <button type="submit" class="btn-gradient-custom me-3">Submit</button>
                    <a href="{{ route('items.index') }}" class="btn-cancel-custom">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Category -> Subcategory
            $('#category_id').on('change', function () {
                var categoryId = $(this).val();
                var subCategorySelect = $('#sub_category_id');
                var childCategorySelect = $('#child_category_id');

                subCategorySelect.html('<option value="">Loading...</option>');
                childCategorySelect.html('<option value="">Select Child Category</option>');

                if (categoryId) {
                    $.ajax({
                        url: "{{ url('admin/get-subcategories') }}/" + categoryId,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            subCategorySelect.html('<option value="">Select Subcategory</option>');
                            $.each(data, function (key, value) {
                                subCategorySelect.append('<option value="' + value.id + '">' + value.title + '</option>');
                            });
                        },
                        error: function () {
                            subCategorySelect.html('<option value="">Error loading subcategories</option>');
                        }
                    });
                } else {
                    subCategorySelect.html('<option value="">Select Subcategory</option>');
                }
            });

            // Subcategory -> Child Category
            $('#sub_category_id').on('change', function () {
                var subCategoryId = $(this).val();
                var childCategorySelect = $('#child_category_id');

                childCategorySelect.html('<option value="">Loading...</option>');

                if (subCategoryId) {
                    $.ajax({
                        url: "{{ url('admin/get-child-categories') }}/" + subCategoryId,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            childCategorySelect.html('<option value="">Select Child Category</option>');
                            $.each(data, function (key, value) {
                                childCategorySelect.append('<option value="' + value.id + '">' + value.title + '</option>');
                            });
                        },
                        error: function () {
                            childCategorySelect.html('<option value="">Error loading child categories</option>');
                        }
                    });
                } else {
                    childCategorySelect.html('<option value="">Select Child Category</option>');
                }
            });
        });

        function previewImage(event) {
            var input = event.target;
            var fileName = input.files.length > 0 ? input.files[0].name : 'No file chosen';
            document.getElementById('fileName').textContent = fileName;

            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('imagePreview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            if (input.files.length > 0) {
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection