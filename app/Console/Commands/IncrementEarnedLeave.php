<?php

// app/Console/Commands/IncrementEarnedLeave.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use App\Models\LeaveBalance;
use App\Models\LeaveType;


class IncrementEarnedLeave extends Command
{
    protected $signature = 'leave:increment-earned-leave';
    protected $description = 'Auto-increment earned leave for all users';
    public function handle()
    {
        $users = FilamentUser::with('leaveBalance')->get();
        $earnedLeaveType = LeaveType::where('name', 'Earned Leave')->first();
    
        foreach ($users as $user) {
            $employmentType = $user->employment_type;
            info("User ID: {$user->id}, Employment: " . json_encode($employmentType));
    
            // Fetch LeaveType and LeavePolicy
            if ($earnedLeaveType) {
                $leavePolicy = $earnedLeaveType->leavePolicy;
    
                if ($leavePolicy) {
                    $leavePlan = $leavePolicy->leavePlan;
    
                    // Add log statements to debug conditions
                    info("User ID: {$user->id}, LeavePlan: " . json_encode($leavePlan));
                    info("User ID: {$user->id}, Probation Period: {$leavePlan->probation_period}");
                    info("User ID: {$user->id}, Regular Period: {$leavePlan->regular_period}");
                    info("User ID: {$user->id}, Contract Period: {$leavePlan->contract_period}");
                    info("User ID: {$user->id}, Notice Period: {$leavePlan->notice_period}");
    
                    // Check if LeavePlan exists
                    if ($leavePlan) {
                        // Check if employment_type conditions are met
                        if (
                            ($employmentType === 'probation_period' && $leavePlan->probation_period) ||
                            ($employmentType === 'regular_period' && $leavePlan->regular_period) ||
                            ($employmentType === 'contract_period' && $leavePlan->contract_period) ||
                            ($employmentType === 'notice_period' && $leavePlan->notice_period)
                        ) {
                            // Fetch and apply LeaveRules
                            if ($leavePolicy) {
                                $leaveRules = $leavePolicy->leaveRules;
    
                                foreach ($leaveRules as $leaveRule) {
                                    $duration = $leaveRule->duration;
    
                                    if ($user->leaveBalance) {
                                        $user->leaveBalance->increment('earned_leave_balance', $duration);
                                        // Add log statement
                                        info("User ID: {$user->id}, Incremened earned leave by $duration days.");
                                    } else {
                                        // Add log statement for missing leave balance
                                        info("User ID: {$user->id}, Missing Leave Balance for Earned Leave.");
                                    }
                                }
                            }
                        } else {
                            // Add log statement for conditions not met
                            info("User ID: {$user->id}, LeavePlan conditions not met for employment type: {$employmentType}.");
                        }
                    } else {
                        // Add log statement for missing leave plan
                        info("User ID: {$user->id}, Missing Leave Plan for Earned Leave.");
                    }
                } else {
                    // Add log statement for missing leave policy
                    info("User ID: {$user->id}, Missing Leave Policy for Earned Leave.");
                }
            } else {
                // Add log statement for missing earned leave type
                info("User ID: {$user->id}, Missing Earned Leave Type.");
            }
        }
    
        // Add final log statement
        info('Earned leave has been incremented for all users.');
    }
    
    

}
