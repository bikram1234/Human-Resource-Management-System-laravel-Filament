<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class nodueapproval extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['no_due_id','date', 'user_id', 'status1','status2','approver_user_id' => 'array','department_approval_id' => 'array'];

    public function nodue()
    {
        return $this->belongsTo(nodue::class, 'no_due_id');
    }
    public function user()
    {
        return $this->belongsTo(MasEmployee::class, 'user_id');
    }
    public function approver()
    {
        return $this->belongsTo(MasEmployee::class, 'approver_user_id');
    }
    public function department_approval()
    {
        return $this->belongsTo(MasEmployee::class, 'department_approval_id');
    }


    public function approvedSectionHeadIDs()
{
    return $this->where('no_due_id', $this->no_due_id)
        ->where('status1', 'approved')
        ->pluck('user_id')
        ->toArray();
}  public function approvedDepartmentHeadIDs()
{
    return $this->where('no_due_id', $this->no_due_id)
        ->where('status1', 'approved')
        ->pluck('user_id')
        ->toArray();
}
}
