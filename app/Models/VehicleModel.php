<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleModel extends Model
{
    protected $fillable = [
        'name',
        'sleep_person',
        'description',
        'inner_image',
        'category_id',
        'base_price',
        'price',
    ];

    public function categories(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
      // One vehicle model has many additional options
    public function addtionalOptions()
    {
        return $this->hasMany(AdditionalOption::class);
    }
}
