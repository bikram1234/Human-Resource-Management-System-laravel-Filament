<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Auth;


class LeaveEncashmentApprovalRule extends Model
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
        return $this->belongsTo(encashment::class, 'type_id');
    }

     // Define the relationship to approval_conditions
     public function approvalConditions()
     {
         return $this->hasMany(LeaveEncashmentApprovalCondition::class, 'approval_rule_id');
     }

     
    public function EncashmentFormulas()
    {
        return $this->hasMany(LeaveEncashmentFormula::class, 'approval_rule_id');
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
