<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class nodue extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['user_id','date', 'reason', 'status'];

    public function user()
    {
        return $this->belongsTo(MasEmployee::class, 'user_id');
    }
    public function approvals()
    {
        return $this->hasMany(nodueapproval::class,'no_due_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($Approval) {
            // Set the casual_leave_balance based on the matched LeaveRule
            $Approval->approvals()->create([
                'no_due_id' => $Approval->id,
                'user_id' => $Approval->user_id,
                'date' => $Approval->date,
                
                
            ]);
        });
    }

    public static function canViewForRecord(Model $ownerRecord): bool
    {
        return $ownerRecord->status === 'approved';
    }


}
