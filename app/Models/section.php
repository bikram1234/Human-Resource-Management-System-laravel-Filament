<?php

namespace App\Models;

use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department_id',
        'status'
    ];

    public function departmentName()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    

    public function users()
    {
        // Define the relationship with the User model
        return $this->hasMany(FilamentUser::class); // Assuming you have a 'users' table
    }
}

