<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
class FuelClaim extends Model
{
    use HasFactory,HasUuids;
    protected $fillable = [
        'user_id',
        'location',
        'application_date',
        'date',
        'vehicle_type',
        'vehicle_no',
        'initial_km',
        'final_km' ,
        'quantity' ,
        'mileage',
        'rate',
        'amount',
        'status',
        'remark',
        'expense_type_id',
        'attachment',
    ];

    public function user()
    {
        return $this->belongsTo(FilamentUser::class,'user_id');
    }
    public function vehicle()
    {
        return $this->belongsTo(VehicleNumber::class,'vehicle_no');
    }
    public function vehicletype()
    {
        return $this->belongsTo(VehicleType::class,'vehicle_type');
    }
    public function FuelApproval()
    {
        return $this->hasOne(FuelApproval::class, 'applied_expense_id');
    }
  
    protected static function boot()
    {
        parent::boot();

        static::created(function ($Expense) {
            // Set the casual_leave_balance based on the matched LeaveRule
            $Expense->FuelApproval()->create([
                'applied_expense_id' => $Expense->id
            ]);
        });
    }

    public static function canViewForRecord(Model $ownerRecord): bool
    {
        return $ownerRecord->status === 'approved';
    }
}
