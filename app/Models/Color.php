<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'image',
        'status',
        'type',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
