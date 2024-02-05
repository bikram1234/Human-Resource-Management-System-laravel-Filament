<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Chiiya\FilamentAccessControl\Models\FilamentUser;



class LoanAdvancetype extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'condition',
        'status',
    ];

    public function applyloan()
    {
        return $this->hasMany(ApplyLoanAdvance::class, 'loan_type_id');
    }
}