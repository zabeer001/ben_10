<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name']; // adjust fields as needed

    public function vehicleModels()
    {
        return $this->hasMany(VehicleModel::class);
    }
}
