<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelColorWiseImage extends Model
{
    use HasFactory;

    protected $fillable = ['vehicle_model_id', 'color_1_id', 'color_2_id', 'image','image2'];

    public function vehicleModel()
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function color1()
    {
        return $this->belongsTo(Color::class, 'color_1_id');
    }

    public function color2()
    {
        return $this->belongsTo(Color::class, 'color_2_id');
    }
}
