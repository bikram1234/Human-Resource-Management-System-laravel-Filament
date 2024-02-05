<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetCode extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'code',
        'particular',
    ];
    public function applyloan()
    {
        return $this->hasMany(ApplyLoanAdvance::class, 'loan_type_id');
    }
}
