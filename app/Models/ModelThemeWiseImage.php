<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelThemeWiseImage extends Model
{
    protected $fillable = [
        'vehicle_model_id',
        'theme_id',
        'image',
    ];

    public function vehicleModel()
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }
}
