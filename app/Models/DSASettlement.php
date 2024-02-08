<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Chiiya\FilamentAccessControl\Models\FilamentUser;




class DSASettlement extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'expensetype_id',
        'date',
        'advance_no',
        'advance_amount',
        'total_amount_adjusted',
        'net_payable_amount',
        'balance_amount',
        'upload_file',
        //'level1',
        //'level2',
        //'level3',
        'status',
        'remark',

    ];

    public function user()
    {
        return $this->belongsTo(FilamentUser::class,'user_id');
    }
    public function advance()
    {
        return $this->belongsTo(ApplyAdvance::class,'advance_no');
    }
    public function expensetype()
    {
        return $this->belongsTo(ExpenseType::class,'expensetype_id');
    }
    public function dsamanual()
    {
        return $this->hasMany(DSAManual::class,'dsa_settlement_id');
    }
    public function DSAApproval()
    {
        return $this->hasOne(DSAApproval::class, 'applied_expense_id');
    }

    
    protected static function boot()
    {
        parent::boot();

        static::created(function ($leave) {
            // Set the casual_leave_balance based on the matched LeaveRule
            $leave->DSAApproval()->create([
                'applied_expense_id' => $leave->id
            ]);
        });
    }

    public static function canViewForRecord(Model $ownerRecord): bool
    {
        return $ownerRecord->status === 'approved';
    }
    

}
