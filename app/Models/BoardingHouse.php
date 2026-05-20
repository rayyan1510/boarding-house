<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class BoardingHouse extends Model
{
    use HasFactory, SoftDeletes;
    //
    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'city_id',
        'category_id',
        'description',
        'price',
        'address',
    ];

    // relasi dengan city
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // relasi dengan category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // relassi dengan room
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    // relasi dengan bonus
    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }

    // relasi dengan testimonial
    public function testimonials()
    {
        return $this->hasMany(Testimonial::class);
    }

    // relasi dengan transaction
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
