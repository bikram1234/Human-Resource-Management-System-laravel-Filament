<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Scopes\CreatedByScope;
use App\Scopes\EditedByScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class ExpenseApprovalRule extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'For',
        'type_id',
        'RuleName',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'edited_by'
    ];
    public function type()
    {
        return $this->belongsTo(ExpenseType::class, 'type_id');
    }

     // Define the relationship to approval_conditions
     public function approvalConditions()
     {
         return $this->hasMany(ExpenseApprovalCondition::class, 'approval_rule_id');
     }

     
    public function ExpenseFormulas()
    {
        return $this->hasMany(LeaveFormula::class, 'approval_rule_id');
    }

     protected static function boot()
     {
         parent::boot();
 
         static::creating(function ($model) {
             if (Auth::check()) {
                 $model->created_by = Auth::id();
             }
         });
 
         static::saving(function ($model) {
             if (Auth::check()) {
                 $model->edited_by = Auth::id();
             }
         });
     }
}
