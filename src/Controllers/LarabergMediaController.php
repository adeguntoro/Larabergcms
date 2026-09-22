<?php

namespace LarabergCms\LarabergCms\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LarabergMediaController
{
    protected function disk(): string
    {
        return config('larabergcms.disk', 'public');
    }

    protected function allowedExtensions(): array
    {
        return config('larabergcms.allowed_extensions', []);
    }

    protected function enforceAllowedExtensions(): bool
    {
        return (bool) config('larabergcms.enforce_allowed_extensions', true);
    }

    protected function looksLikeHtmlOrSvg(string $content): bool
    {
        return (bool) preg_match(
            '/<\s*(html|head|body|script|svg|img|link|iframe|object|embed|style)[\s>]/i',
            substr($content, 0, 4096)
        );
    }

    protected function ensureAllowedExtension(string $extension): bool
    {
        if (! $this->enforceAllowedExtensions()) {
            return true;
        }

        return in_array(strtolower($extension), $this->allowedExtensions(), true);
    }

    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            return $this->storeMultipart($request);
        }

        return $this->storeRaw($request);
    }

    protected function storeMultipart(Request $request)
    {
        $file = $request->file('file');

        $request->validate([
            'file' => ['required', 'file', 'max:' . config('larabergcms.max_upload_kb', 51200)],
        ]);

        if (! $this->ensureAllowedExtension($file->getClientOriginalExtension())) {
            return response()->json(['message' => 'File type not allowed.'], 422);
        }

        $path = $file->store('laraberg', $this->disk());

        return response()->json([
            'id' => pathinfo($path, PATHINFO_FILENAME),
            'url' => Storage::disk($this->disk())->url($path),
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    protected function storeRaw(Request $request)
    {
        $content = $request->getContent();

        if ($content === '') {
            return response()->json(['message' => 'No file content provided.'], 422);
        }

        $maxBytes = config('larabergcms.max_upload_kb', 51200) * 1024;

        if (strlen($content) > $maxBytes) {
            return response()->json(['message' => 'File exceeds the maximum allowed size.'], 422);
        }

        $originalName = $request->header('X-File-Name', 'upload');
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (! $this->ensureAllowedExtension($extension)) {
            return response()->json(['message' => 'File type not allowed.'], 422);
        }

        if ($this->looksLikeHtmlOrSvg($content)) {
            return response()->json(['message' => 'HTML/SVG content is not allowed.'], 422);
        }

        $name = Str::random(32);
        $path = 'laraberg/' . $name . '.' . $extension;

        Storage::disk($this->disk())->put($path, $content);

        return response()->json([
            'id' => $name,
            'url' => Storage::disk($this->disk())->url($path),
            'name' => $originalName,
            'mime' => $request->header('X-File-Type', 'application/octet-stream'),
            'size' => strlen($content),
        ]);
    }
}