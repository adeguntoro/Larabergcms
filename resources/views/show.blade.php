<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $laraberg->title }} - {{ config('app.name', 'Laravel') }}</title>

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
            <h1 class="h3 mb-0">{{ $laraberg->title }}</h1>
            <div>
                <a href="{{ route('larabergcms.index') }}" class="btn btn-outline-secondary btn-sm">Back to listing</a>
                <a href="{{ route('larabergcms.edit', $laraberg) }}" class="btn btn-outline-primary btn-sm">Edit</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <dl class="row">
            <dt class="col-sm-2">Slug</dt>
            <dd class="col-sm-10"><code>{{ $laraberg->slug }}</code></dd>

            <dt class="col-sm-2">Status</dt>
            <dd class="col-sm-10">
                <span class="badge text-bg-{{ $laraberg->status === 'published' ? 'success' : ($laraberg->status === 'draft' ? 'secondary' : 'warning') }}">
                    {{ ucfirst($laraberg->status) }}
                </span>
            </dd>

            @if ($laraberg->category)
                <dt class="col-sm-2">Category</dt>
                <dd class="col-sm-10">{{ $laraberg->category->name }}</dd>
            @endif

            <dt class="col-sm-2">Published At</dt>
            <dd class="col-sm-10">{{ $laraberg->published_at ? $laraberg->published_at->format('d M Y H:i') : '—' }}</dd>

            @if ($laraberg->image)
                <dt class="col-sm-2">Image</dt>
                <dd class="col-sm-10">
                    <img src="{{ $laraberg->image }}" alt="{{ $laraberg->title }}" class="img-thumbnail" style="max-height: 200px;">
                </dd>
            @endif

            <dt class="col-sm-2">Created By</dt>
            <dd class="col-sm-10">User #{{ $laraberg->user_id }}</dd>
        </dl>

        <hr>

        <div class="laraberg-content">
            {!! $laraberg->render() !!}
        </div>
    </div>
</body>
</html>