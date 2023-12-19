<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AppliedEncashment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'date',
        'number_of_days',
        'amount',
        'remark',
        'status'
    ];
    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(FilamentUser::class);
    }
    public function EncashmentApproval()
    {
        return $this->hasOne(EncashmentApproval::class, 'applied_encashment_id');
    }
    protected static function boot()
    {
        parent::boot();

        static::created(function ($encashment) {
            // Set the casual_leave_balance based on the matched LeaveRule
            $encashment->EncashmentApproval()->create([
                'applied_encashment_id' => $encashment->id
            ]);
        });
    }

    public static function canViewForRecord(Model $ownerRecord): bool
    {
        return $ownerRecord->status === 'approved';
    }}
