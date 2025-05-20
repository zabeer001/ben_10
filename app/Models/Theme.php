<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'flooring_name',
        'cabinetry_1_name',
        'table_top_1_name',
        'seating_1_name',
        'image',
        'flooring_image',
        'cabinetry_1_image',
        'table_top_1_image',
        'seating_1_image',
    ];
    
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
