<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    // Implementasi metode untuk mengelola data kategori
    public function getAllCategories()
    {
        // Logika untuk mengambil semua data kategori dari database
        // Contoh: return Category::all();
        return Category::all();
    }

    public function getCategoryBySlug($slug)
    {
        return Category::where('slug', $slug)->firstOrFail();
    }
}
