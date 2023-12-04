<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\CreatedByScope;
use App\Scopes\EditedByScope;

class TransferClaimApproval extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'applied_expense_id',
        'level1',
        'level2',
        'level3',
        'status',
        'remark',
        'created_by',
        'edited_by'
    ];
    

    public function TransferApply() {
        return $this->belongsTo(TransferClaim::class, 'applied_expense_id');
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
