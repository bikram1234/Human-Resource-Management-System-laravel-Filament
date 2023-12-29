<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Scopes\CreatedByScope;
use App\Scopes\EditedByScope;
use Illuminate\Support\Str;


class LeaveRule extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        "policy_id",
        "grade_id",
        "duration",
        "uom",
        "start_date",
        "end_date",
        "islossofpay",
        "employee_type",
        "status",
        'created_by',
        'edited_by',
    ];

    public function policy()
    {
        return $this->belongsTo(LeavePolicy::class, 'policy_id');
    }

    public function grade() {
        return $this->belongsTo(MasGrade::class, 'grade_id');
    }

    public function leaves()
    {
        return $this->hasMany(LeaveType::class); 
    }

    protected $isProcessingCreatingEvent = false;

    // Add the attribute to the $hidden array to make it non-persistent
    protected $hidden = ['isProcessingCreatingEvent'];

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

        
            static::creating(function ($model) {
                // Check if the event is already being processed to avoid infinite loop
                if ($model->isProcessingCreatingEvent) {
                   $model->id = Str::uuid(); // Generate a new UUID for the duplicated model
                    return;
                }
        
                info('Creating event fired');
        
                $originalGrade = $model->grade_id;
                $originalGrade = is_string($originalGrade) ? [$originalGrade] : $originalGrade;
        
                $originalAttributes = $model->getAttributes();
                unset($originalAttributes['grade']);
        
                info('Original Grade:', $originalGrade);
                info('Original Attributes:', $originalAttributes);
        
                // Set the flag to indicate that the event is being processed
                $model->isProcessingCreatingEvent = true;
        
                foreach ($originalGrade as $gradeId) {
                    info('Loop start');
        
                    try {
                        $newModel = clone $model;
                        $newModel->grade_id = $gradeId;
        
                        info('Duplicated Model:', $newModel->toArray());
        
                        // Check if the model is new (not saved)
                        if (!$newModel->exists) {
                            if ($newModel->save()) {
                                info("Duplicated model saved: " . json_encode($newModel->toArray()));
                            } else {
                                info("Error saving duplicated model. Save returned false. Model: " . json_encode($newModel->toArray()));
                            }
                        } else {
                            info("Duplicated model already exists, skipping.");
                        }
                    } catch (\Exception $e) {
                        info('Exception during loop: ' . $e->getMessage());
                    }
        
                    info('Loop end');
                }
        
                info('Original Model Not Saved');
        
                // Reset the flag after processing the event
                $model->isProcessingCreatingEvent = false;
        
                // Prevent the original record from being saved
                return false;
            });
   
            static::saving(function ($model) {
               $grade = $model->grade_id;
               $policyId = $model->policy_id;
               $employee_type = $model->employee_type;
   
               // Check if a record with the same grade, region, and policy_id already exists
               $existingRecord = self::where(function ($query) use ($grade, $policyId, $employee_type) {
                   $query->where('grade_id', $grade)
                       ->where('employee_type', $employee_type)
                       ->where('policy_id', $policyId);
               })->first();
   
               if ($existingRecord) {
                   // Record with the same grade, region, and policy_id already exists, prevent saving
                   throw new \Exception("Record with the same grade, region, and policy_id already exists.");
               }
           });
        
    }
}
