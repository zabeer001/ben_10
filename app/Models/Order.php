<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    public function additionalOption()
    {
        return $this->belongsTo(AdditionalOption::class);
    }


    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function vehicleModel()
    {
        return $this->belongsTo(VehicleModel::class);
    }


    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }
}
