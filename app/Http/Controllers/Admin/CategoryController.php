<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::query()
            ->withCount('items')
            ->when($request->filled('q'), fn ($q) => $q->where('nama', 'like', "%{$request->q}%"))
            ->orderBy('nama')
            ->paginate(12)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $this->authorize('create', Category::class);

        return view('admin.categories.create');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $category = Category::create($request->validated());
        ActivityLogger::created($category, "Menambah kategori \"{$category->nama}\".");

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());
        ActivityLogger::updated($category, "Mengubah kategori \"{$category->nama}\".");

        return $this->backToList('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        if ($category->items()->exists()) {
            return back()->with('error', 'Kategori masih digunakan oleh barang dan tidak dapat dihapus.');
        }

        $category->forcedelete();
        ActivityLogger::deleted($category, "Menghapus kategori \"{$category->nama}\".");

        return back()
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
