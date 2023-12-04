<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Log;



class TransferClaim extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'expense_type_id',
        'user_id',
        'employee_id',
        'date',
        'designation',
        'department',
        'basic_pay',
        'transfer_claim_type',
        'current_location',
        'new_location',
        'claim_amount',
        'distance_km',
        'level1',
        'level2',
        'level3',
        'status',
        'attachment',
        'remark',

    ];
    public function user()
    {
        return $this->belongsTo(MasEmployee::class,'user_id');
    }
    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class,'expense_type_id');
    }
    public function pay()
    {
        return $this->belongsTo(MasGradeStep::class,'basic_pay');
    }
    public function TransferClaimApproval()
    {
        return $this->hasOne(TransferClaimApproval::class, 'applied_expense_id');
    }
  
    protected static function boot()
    {
        parent::boot();

        static::created(function ($Expense) {
            // Set the casual_leave_balance based on the matched LeaveRule
            $Expense->TransferApproval()->create([
                'applied_expense_id' => $Expense->id
            ]);
        });
    }

    public static function canViewForRecord(Model $ownerRecord): bool
    {
        return $ownerRecord->status === 'approved';
    }
}
