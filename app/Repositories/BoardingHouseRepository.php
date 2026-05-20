<?php

namespace App\Repositories;

use App\Interfaces\BoardingHouseRepositoryInterface;
use App\Models\BoardingHouse;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;

class BoardingHouseRepository implements BoardingHouseRepositoryInterface
{
    // Implementasi metode untuk mengelola data boarding house
    public function getAllBoardingHouses($search = null, $city = null, $category = null)
    {
        // Logika untuk mengambil semua data boarding house dari database
        // Contoh: return BoardingHouse::all();
        $query = BoardingHouse::query();

        // fungsi pencarian
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // fungsi pencarian berdasarkan slug kota
        if ($city) {
            $query->whereHas('city', function (Builder $query) use ($city) {
                $query->where('slug', $city);
            });
        }

        // fungsi pencarian berdasarkan slug kategori
        if ($category) {
            $query->whereHas('category', function (Builder $query) use ($category) {
                $query->where('slug', $category);
            });
        }

        return $query->get();
    }

    // fungsi untuk mengambil data boarding house yang paling populer berdasarkan jumlah transaksi
    public function getPopularBoardingHouses($limit = 5)
    {
        return BoardingHouse::withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->limit($limit)
            ->get();
    }

    // fungsi untuk mengambil data boarding house berdasarkan slug kota
    public function getBoardingHouseByCitySlug($slug)
    {
        return BoardingHouse::whereHas('city', function (Builder $query) use ($slug) {
            $query->where('slug', $slug);
        })->get();
    }

    // fungsi untuk mengambil data boarding house berdasarkan slug kategori
    public function getBoardingHouseByCategorySlug($slug)
    {
        return BoardingHouse::whereHas('category', function (Builder $query) use ($slug) {
            $query->where('slug', $slug);
        })->get();
    }

    // fungsi untuk mengambil data boarding house berdasarkan slug boarding house
    public function getBoardingHouseBySlug($slug)
    {
        return BoardingHouse::where('slug', $slug)->first();
    }

    public function getBoardingHouseRoomById($id)
    {
        return Room::find($id);
    }
}
