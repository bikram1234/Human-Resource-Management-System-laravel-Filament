<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class VehicleNumber extends Model
{
    use HasFactory,HasUuids;

    protected $fillable = [
        'vehicle_type',
        'vehicle_number',
        'vehicle_mileage',
        'status',
 
     ];
      public function vehicletype()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type');
    }
    public function fuel()
    {
        return $this->hasMany(FuelClaim::class, 'vehicle_no');
    }
}
