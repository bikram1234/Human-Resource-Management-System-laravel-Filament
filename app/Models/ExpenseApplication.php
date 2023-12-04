<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\CreatedByScope;
use App\Scopes\EditedByScope;

class ExpenseApplication extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'expense_type_id',
        'application_date',
        'total_amount',
        'description',
        'attachment',
        'travel_type',
        'travel_mode',
        'travel_from_date',
        'travel_to_date',
        'travel_from',
        'travel_to',
        'level1',
        'level2',
        'level3',
        'status',
        'remark',
    ];

    public function user()
    {
        return $this->belongsTo(MasEmployee::class, 'user_id');
    }

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class,'expense_type_id');
    }
    public function ExpenseApproval()
    {
        return $this->hasOne(ExpenseApproval::class, 'applied_expense_id');
    }
  
    protected static function boot()
    {
        parent::boot();

        static::created(function ($Expense) {
            // Set the casual_leave_balance based on the matched LeaveRule
            $Expense->ExpenseApproval()->create([
                'applied_expense_id' => $Expense->id
            ]);
        });
    }

    public static function canViewForRecord(Model $ownerRecord): bool
    {
        return $ownerRecord->status === 'approved';
    }
    
    
    
    

}
