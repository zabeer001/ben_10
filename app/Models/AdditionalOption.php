<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalOption extends Model
{
    use HasFactory;

    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'vehicle_model_id',
        'category_name',
        'type',
    ];
    public function vehicleModel()
    {
        return $this->belongsTo(VehicleModel::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
