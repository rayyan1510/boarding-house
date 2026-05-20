<?php

namespace App\Repositories;

use App\Interfaces\CityRepositoryInterface;
use App\Models\City;

class CityRepository implements CityRepositoryInterface
{
    // Implementasi metode untuk mengelola data kota
    public function getAllCities()
    {
        // Logika untuk mengambil semua data kota dari database
        // Contoh: return City::all();
        return City::all();
    }

    public function getCityBySlug($slug)
    {
        return City::where('slug', $slug)->firstOrFail();
    }
}
