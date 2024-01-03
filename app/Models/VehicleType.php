<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class VehicleType extends Model
{
    use HasFactory,HasUuids;

    protected $fillable = [
        'vehicle_type',
 
     ];

     public function vehiclenumber()
     {
         return $this->hasMany(VehicleNumber::class, 'vehicle_type');
     }
     public function fuel()
     {
         return $this->hasMany(FuelClaim::class, 'vehicle_type');
     }
}
