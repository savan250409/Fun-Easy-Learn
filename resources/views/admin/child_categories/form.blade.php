@extends('layouts.app')

@section('content')
    <div class="page-header-custom" style="margin-bottom: 1.5rem; border-bottom: none; padding-bottom: 0;">
        <div>
            <h3 class="page-title-custom">
                <i class="mdi mdi-file-tree page-title-icon-custom"></i>
                {{ isset($childCategory) ? 'Edit Child Category' : 'Add New Child Category' }}
            </h3>
            <p class="page-subtitle-custom">
                {{ isset($childCategory) ? 'Modify an existing child category' : 'Create a new Child Category' }}
            </p>
        </div>
        <div>
            <a href="{{ route('child-categories.index') }}" class="btn-back-custom">
                <i class="mdi mdi-arrow-left"></i> Back to Child Categories
            </a>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-body" style="padding: 2rem;">
            <form
                action="{{ isset($childCategory) ? route('child-categories.update', $childCategory->id) : route('child-categories.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($childCategory))
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
                    <label for="sub_category_id" class="form-label-custom d-block">Parent Subcategory</label>
                    <select class="form-control-custom @error('sub_category_id') is-invalid @enderror" id="sub_category_id"
                        name="sub_category_id" required>
                        <option value="">Select Subcategory</option>
                        @foreach($subCategories as $subCat)
                            <option value="{{ $subCat->id }}" {{ old('sub_category_id', $childCategory->sub_category_id ?? '') == $subCat->id ? 'selected' : '' }}>
                                {{ $subCat->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('sub_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label for="title" class="form-label-custom d-block">Child Category Name</label>
                    <input type="text" class="form-control-custom @error('title') is-invalid @enderror" id="title"
                        name="title" value="{{ old('title', $childCategory->title ?? '') }}"
                        placeholder="Child Category Name" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label for="key" class="form-label-custom d-block">Key</label>
                    <input type="text" class="form-control-custom @error('key') is-invalid @enderror" id="key" name="key"
                        value="{{ old('key', $childCategory->key ?? '') }}" placeholder="Unique identifier" required>
                    @error('key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label-custom d-block">Status</label>
                    <label class="custom-checkbox-label">
                        <input type="checkbox" name="status" value="1" {{ old('status', $childCategory->status ?? 1) ? 'checked' : '' }}>
                        Active
                    </label>
                </div>

                <div class="mb-5">
                    <label for="image" class="form-label-custom d-block">Child Category Image</label>
                    <div class="file-upload-wrapper">
                        <label class="file-upload-btn" for="image">Choose Image</label>
                        <div class="file-upload-text" id="fileName">
                            {{ isset($childCategory) && $childCategory->image ? '1 file attached' : 'No file chosen' }}
                        </div>
                        <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                    </div>
                    @error('image')<div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>@enderror

                    <div class="mt-3">
                        <img id="imagePreview"
                            src="{{ isset($childCategory) && $childCategory->image ? asset('upload/' . $childCategory->image) : '#' }}"
                            alt="Image Preview"
                            style="display: {{ isset($childCategory) && $childCategory->image ? 'block' : 'none' }}; max-width: 150px; border-radius: 4px; border: 1px solid #eaeaea; padding: 3px;">
                    </div>
                </div>

                <div class="d-flex mt-4">
                    <button type="submit" class="btn-gradient-custom me-3">Submit</button>
                    <a href="{{ route('child-categories.index') }}" class="btn-cancel-custom">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#category_id').on('change', function () {
                var categoryId = $(this).val();
                var subCategorySelect = $('#sub_category_id');

                subCategorySelect.html('<option value="">Loading...</option>');

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