<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\CreatedByScope;
use App\Scopes\EditedByScope;

class EncashmentApproval extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'applied_encashment_id',
        'level1',
        'level2',
        'level3',
        'status',
        'remark',
        'created_by',
        'edited_by'
    ];
    

    public function EncashmentApply() {
        return $this->belongsTo(AppliedEncashment::class, 'applied_encashment_id');
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
}
