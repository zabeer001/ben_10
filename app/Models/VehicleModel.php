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
    ];

    public function categories(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
