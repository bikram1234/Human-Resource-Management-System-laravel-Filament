<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use App\Models\LeaveType;

class YearEndProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:year-end-process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Year-end leave balance processing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch all users who are eligible for year-end leave balance update
        $users = FilamentUser::with('leaveBalance')->get();

        foreach ($users as $user) {
            info("Processing user ID: {$user->id}");
        
            $casualLeaveType = LeaveType::where('name', 'Casual Leave')->first();
        
            if ($casualLeaveType) {
                $leavePolicy = $casualLeaveType->leavePolicy;
        
                if ($leavePolicy) {
                    $leaveRules = $leavePolicy->leaveRules;
        
                    if ($leaveRules->isNotEmpty()) {
                        // Check if leaveBalance relationship is loaded
                        if ($user->leaveBalance) {
                            // Get the current casual_leave_balance
                            $casualLeaveBalance = $user->leaveBalance->casual_leave_balance;
                            info("User ID: {$user->id}, Current Casual Leave Balance: {$casualLeaveBalance}");
        
                            // Check if the user is eligible for the year-end process based on LeaveRule condition
                            $eligible = false;
        
                            foreach ($leaveRules as $leaveRule) {
                                $ruleGradeId = $leaveRule->grade_id;
                                $ruleEmploymentType = $leaveRule->employee_type;
        
                                // Check if the LeaveRule's grade_id and employment_type match the user's grade_id and employment_type
                                if ($user->grade_id === $ruleGradeId && $user->employment_type === $ruleEmploymentType) {
                                    // Increment the user's earned_leave balance by the LeaveRule's duration
                                    $user->leaveBalance->increment('earned_leave_balance', $leaveRule->duration);
                                    info("User ID: {$user->id}, Incremented earned_leave_balance by {$leaveRule->duration} days");
        
                                    // Reset the casual_leave_balance to its default value
                                    $user->leaveBalance->casual_leave_balance = $leaveRule->duration; // Set to the LeaveRule's duration
                                    $user->leaveBalance->save();
        
                                    info("User ID: {$user->id}, Casual Leave Balance reset to default: {$leaveRule->duration}");
        
                                    $eligible = true;
                                    break; // Break the loop as we have found a matching LeaveRule
                                }
                            }
        
                            // If no matching LeaveRule is found, log a message
                            if (!$eligible) {
                                info("User ID: {$user->id}, User not eligible for year-end process based on LeaveRule condition.");
                            }
                        } else {
                            // Log a message for users without leaveBalance
                            info("User ID: {$user->id}, Missing Leave Balance for Casual Leave.");
                        }
                    } else {
                        info("No Leave Rules found for Casual Leave.");
                    }
                } else {
                    info("No Leave Policy found for Casual Leave.");
                }
            } else {
                info("Casual Leave Type not found.");
            }
        }
        
        info('Year-end leave balance processing completed.');
    }


    /**
     * Check if the user is eligible for the year-end process based on LeaveRule condition.
     *
     * @param \Chiiya\FilamentAccessControl\Models\FilamentUser $user
     * @param \Illuminate\Database\Eloquent\Collection $leaveRules
     * @return bool
     */
   
}
