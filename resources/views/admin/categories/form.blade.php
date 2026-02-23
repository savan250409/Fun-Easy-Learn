@extends('layouts.app')

@section('content')
    <div class="page-header-custom" style="margin-bottom: 1.5rem; border-bottom: none; padding-bottom: 0;">
        <div>
            <h3 class="page-title-custom">
                <i class="mdi mdi-image-auto-adjust page-title-icon-custom"></i>
                {{ isset($category) ? 'Edit Category' : 'Add New Category' }}
            </h3>
            <p class="page-subtitle-custom">{{ isset($category) ? 'Modify an existing category' : 'Create a new Category' }}
            </p>
        </div>
        <div>
            <a href="{{ route('categories.index') }}" class="btn-back-custom">
                <i class="mdi mdi-arrow-left"></i> Back to Categories
            </a>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-body" style="padding: 2rem;">
            <form action="{{ isset($category) ? route('categories.update', $category->id) : route('categories.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($category))
                    @method('PUT')
                @endif

                <div class="mb-4">
                    <label for="title" class="form-label-custom d-block">Name</label>
                    <input type="text" class="form-control-custom @error('title') is-invalid @enderror" id="title"
                        name="title" value="{{ old('title', $category->title ?? '') }}" placeholder="Category Name"
                        required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label for="key" class="form-label-custom d-block">Key</label>
                    <input type="text" class="form-control-custom @error('key') is-invalid @enderror" id="key" name="key"
                        value="{{ old('key', $category->key ?? '') }}" placeholder="Unique identifier" required>
                    @error('key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label-custom d-block">Status</label>
                    <label class="custom-checkbox-label">
                        <input type="checkbox" name="status" value="1" {{ old('status', $category->status ?? 1) ? 'checked' : '' }}>
                        Active
                    </label>
                </div>

                <div class="mb-5">
                    <label for="image" class="form-label-custom d-block">Category Image</label>
                    <div class="file-upload-wrapper">
                        <label class="file-upload-btn" for="image">Choose Image</label>
                        <div class="file-upload-text" id="fileName">
                            {{ isset($category) && $category->image ? '1 file attached' : 'No file chosen' }}
                        </div>
                        <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                    </div>
                    @error('image')<div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>@enderror

                    <div class="mt-3">
                        <img id="imagePreview"
                            src="{{ isset($category) && $category->image ? asset('upload/' . $category->image) : '#' }}"
                            alt="Image Preview"
                            style="display: {{ isset($category) && $category->image ? 'block' : 'none' }}; max-width: 150px; border-radius: 4px; border: 1px solid #eaeaea; padding: 3px;">
                    </div>
                </div>

                <div class="d-flex mt-4">
                    <button type="submit" class="btn-gradient-custom me-3">Submit</button>
                    <a href="{{ route('categories.index') }}" class="btn-cancel-custom">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
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