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
        'external_base_colour_id',
        'external_decay_colour_id',
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

    public function externalBaseColour()
    {
        return $this->belongsTo(Color::class, 'external_base_colour_id');
    }

    public function externalDecayColour()
    {
        return $this->belongsTo(Color::class, 'external_decay_colour_id');
    }
}
