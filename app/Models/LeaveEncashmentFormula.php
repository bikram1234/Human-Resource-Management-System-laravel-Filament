<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Auth;


class LeaveEncashmentFormula extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'approval_rule_id',
        'condition',
        'field',
        'operator',
        'value',
        'employee_id',
        'created_by', 
        'updated_by'
    ];

    public function employee() {
        return $this->belongsTo(MasEmployee::class, 'employee_id');
    }

    public function approvalRule()
    {
        return $this->belongsTo(LeaveEncashmentApprovalRule::class, 'approval_rule_id');
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
