<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('complaints')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori ini sudah ada.',
        ]);

        Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => true,
        ]);

        return back()->with('success', 'Kategori pengaduan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $category->update($validated);

        return back()->with('success', 'Kategori pengaduan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $category = Category::withCount('complaints')->findOrFail($id);

        if ($category->complaints_count > 0) {
            // Nonaktifkan saja jika sudah ada pengaduan terkait
            $category->update(['is_active' => false]);
            return back()->with('info', 'Kategori tidak dapat dihapus karena sudah memiliki data pengaduan. Status telah dinonaktifkan.');
        }

        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}

