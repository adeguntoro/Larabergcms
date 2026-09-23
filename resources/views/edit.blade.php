<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Post - {{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/vendor/larabergcms/larabergcms.css', 'resources/vendor/larabergcms/larabergcms.js'])
    <link rel="stylesheet" href="{{ asset('vendor/laraberg/css/laraberg.css') }}">
    <style>
        body { font-family: 'Source Sans 3', system-ui, sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Playfair Display', Georgia, serif; }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Edit Post</h1>
            <a href="{{ route('larabergcms.index') }}" class="btn btn-outline-secondary btn-sm">Back to listing</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('larabergcms.update', $laraberg) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" id="title" name="title"
                           class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $laraberg->title) }}" required>

                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" id="slug" name="slug"
                           class="form-control @error('slug') is-invalid @enderror"
                           value="{{ old('slug', $laraberg->slug) }}" placeholder="optional">

                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="category_id" class="form-label">Category</label>
                    <select id="category_id" name="category_id"
                            class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">Select a category</option>
                        @foreach ($categories ?? collect([]) as $category)
                            <option value="{{ $category->id }}"
                                {{ (string) old('category_id', $laraberg->category_id) === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->name ?? $category->title }}
                            </option>
                        @endforeach
                    </select>

                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status"
                            class="form-select @error('status') is-invalid @enderror">
                        @foreach (['draft', 'published', 'archived'] as $status)
                            <option value="{{ $status }}"
                                {{ old('status', $laraberg->status) === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>

                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="published_at" class="form-label">Published At</label>
                    <input type="datetime-local" id="published_at" name="published_at"
                           class="form-control @error('published_at') is-invalid @enderror"
                           value="{{ old('published_at', $laraberg->published_at?->format('Y-m-d\TH:i')) }}">

                    @error('published_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="image" class="form-label">Image URL</label>
                    <input type="url" id="image" name="image"
                           class="form-control @error('image') is-invalid @enderror"
                           value="{{ old('image', $laraberg->image) }}" placeholder="https://... optional">

                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="content" class="form-label">Content</label>
                    <textarea id="content" name="content"
                              class="form-control @error('content') is-invalid @enderror"
                              hidden>{{ old('content', $laraberg->content) }}</textarea>

                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <input type="hidden" name="user_id" value="{{ old('user_id', $laraberg->user_id ?? auth()->id()) }}">

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Loaded as a module so it executes after the deferred @vite entry that
         publishes window.React / window.ReactDOM (see resources/vendor/larabergcms).
         A classic script runs during parsing, before deferred modules, which
         leaves the global `Laraberg` undefined. --}}
    <script type="module" src="{{ asset('vendor/laraberg/js/laraberg.js') }}"></script>

    <script>
        function mediaUpload({ filesList, onError, onFileChange }) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            Array.from(filesList || []).forEach((file) => {
                fetch('{{ route('larabergcms.media') }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': file.type || 'application/octet-stream',
                        'X-File-Name': file.name || 'upload',
                        'X-File-Type': file.type || 'application/octet-stream',
                    },
                    body: file,
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error('Upload failed');
                        }
                        return response.json();
                    })
                    .then((attachment) => {
                        onFileChange([{ id: attachment.id, url: attachment.url }]);
                    })
                    .catch(() => onError('The file could not be uploaded.'));
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            Laraberg.init('content', { height: '600px', mediaUpload });

            var mainForm = document.querySelector('form[method="POST"][action="{{ route('larabergcms.update', $laraberg) }}"]');
            if (mainForm) {
                mainForm.addEventListener('submit', function () {
                    var el = document.getElementById('content');
                    if (el) {
                        el.value = el.innerText || el.textContent || '';
                    }
                });
            }
        });
    </script>
</body>
</html>