<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Chiiya\FilamentAccessControl\Models\FilamentUser;



class ApplyLoanAdvance extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'reference_no','user_id', 'loan_type_id','date', 'activity','subject', 'from_date', 'to_date','amount', 'budget_code', 'attachment', 'status', 'remark'
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(FilamentUser::class, 'user_id');
    }
    public function loantype()
    {
        return $this->belongsTo(LoanAdvancetype::class, 'loan_type_id');
    }
}
