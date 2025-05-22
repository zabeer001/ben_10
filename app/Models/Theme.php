<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'flooring_name',
        'flooring_image',
        'cabinetry_1_name',
        'cabinetry_1_image',
        'cabinetry_2_name',
        'cabinetry_2_image',
        'table_top_1_name',
        'table_top_1_image',
        'table_top_2_name',
        'table_top_2_image',
        'seating_1_name',
        'seating_1_image',
        'seating_2_name',
        'seating_2_image',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function modelThemeWiseImages()
    {
        return $this->hasMany(ModelThemeWiseImage::class);
    }
}
