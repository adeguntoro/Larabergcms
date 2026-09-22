<?php

namespace LarabergCms\LarabergCms\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use LarabergCms\LarabergCms\Models\Category;
use LarabergCms\LarabergCms\Models\Laraberg;

class LarabergController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Laraberg::latest()->paginate(10);

        return view('larabergcms::index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('larabergcms::create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer'],
            'user_id' => ['nullable', 'integer'],
            'published_at' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:draft,published,archived'],
        ]);

        $slug = $validated['slug'] ?: Str::slug($validated['title']);
        $originalSlug = $slug;
        $i = 1;
        while (Laraberg::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i++;
        }

        Laraberg::create([
            ...$validated,
            'slug' => $slug,
            // user_id is nullable — leave as-is for guests.
            // If you want to store the authenticated user's id, do this:
            // 'user_id' => auth()->id(),
            'user_id' => $validated['user_id'] ?? auth()->id(),
            'status' => $validated['status'] ?? 'draft',
        ]);

        return redirect()
            ->route('larabergcms.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Laraberg $laraberg)
    {
        return view('larabergcms::show', compact('laraberg'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Laraberg $laraberg)
    {
        $categories = Category::orderBy('name')->get();

        return view('larabergcms::edit', compact('laraberg', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Laraberg $laraberg)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer'],
            'user_id' => ['nullable', 'integer'],
            'published_at' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:draft,published,archived'],
        ]);

        $laraberg->update([
            ...$validated,
            // user_id is nullable — leave as-is for guests.
            // If you want to store the authenticated user's id, do this:
            // 'user_id' => auth()->id(),
            'user_id' => $validated['user_id'] ?? auth()->id(),
            'status' => $validated['status'] ?? 'draft',
        ]);

        return redirect()
            ->route('larabergcms.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Laraberg $laraberg)
    {
        $laraberg->delete();

        return redirect()
            ->route('larabergcms.index')
            ->with('success', 'Post deleted successfully.');
    }
}