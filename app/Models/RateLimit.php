<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RateLimit extends Model
{
    use HasFactory, HasUuids;
     // Define the table associated with the model
     protected $table = 'rate_limits';

     // Define the fillable fields (columns)
     protected $fillable = [
         'grade',
         'region',
         'limit_amount',
         'start_date',
         'end_date',
         'status',
         'policy_id',
         // Add other fields here as needed
     ];
 
     // Define relationships if applicable
     public function policy()
     {
         return $this->belongsTo(Policy::class);
     }
     public function gradeName()
     {
     return $this->belongsTo(MasGrade::class, 'grade'); // 'grade' is the foreign key in RateLimit
     }

     public function regionname()
     {
     return $this->belongsTo(region::class, 'region'); // 'region' is the foreign key in RateLimit
     }

     protected $isProcessingCreatingEvent = false;

    // Add the attribute to the $hidden array to make it non-persistent
    protected $hidden = ['isProcessingCreatingEvent'];

     protected static function boot()
     {
         parent::boot();
     
         static::creating(function ($model) {
             // Check if the event is already being processed to avoid infinite loop
             if ($model->isProcessingCreatingEvent) {
                $model->id = Str::uuid(); // Generate a new UUID for the duplicated model
                 return;
             }
     
             info('Creating event fired');
     
             $originalGrade = $model->grade;
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
                     $newModel->grade = $gradeId;
     
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

         static::creating(function ($model) {
            $grade = $model->grade;
            $region = $model->region;
            $policyId = $model->policy_id;

            // Check if a record with the same grade, region, and policy_id already exists
            $existingRecord = self::where(function ($query) use ($grade, $region, $policyId) {
                $query->where('grade', $grade)
                    ->where('region', $region)
                    ->where('policy_id', $policyId);
            })->first();

            if ($existingRecord) {
                // Record with the same grade, region, and policy_id already exists, prevent saving
                throw new \Exception("Record with the same grade, region, and policy_id already exists.");
            }
        });
     }


}
