<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Chiiya\FilamentAccessControl\Models\FilamentUser;

class AdvanceSettlement extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'loantype_id',
        'date',
        'loan_advance_id',
        'advance_amount',
        'balance_amount',
        'attachment',
        'status',
        'remark',


    ];

    public function user()
    {
        return $this->belongsTo(FilamentUser::class,'user_id');
    }
    public function loanadvance()
    {
        return $this->belongsTo(ApplyLoanAdvance::class,'loan_advance_id');
    }
    public function loantype()
    {
        return $this->belongsTo(LoanAdvancetype::class,'loantype_id');
    }
}
