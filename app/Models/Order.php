<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_model_id',
        'theme_id',
        'base_price',
        'total_price',
    ];


    public function additionalOptions()
    {
        return $this->belongsToMany(AdditionalOption::class);
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class);
    }

    public function vehicleModel()
    {
        return $this->belongsTo(VehicleModel::class);
    }


    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

     public function customerInfo()
    {
        return $this->belongsTo(CustomerInfo::class);
    }
}
