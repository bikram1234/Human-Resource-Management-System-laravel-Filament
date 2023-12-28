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
    protected $description = 'Command description';

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
                        $defaultCasualLeaveBalance = $leaveRules->first()->duration;
                    } else {
                        $defaultCasualLeaveBalance = 0.0;
                    }
                } else {
                    $defaultCasualLeaveBalance = 0.0;
                }
            } else {
                $defaultCasualLeaveBalance = 0.0;
            }
    
            info("User ID: {$user->id}, Default Casual Leave Balance: {$defaultCasualLeaveBalance}");
    
            // Check if leaveBalance relationship is loaded
            if ($user->leaveBalance) {
                // Get the current casual_leave_balance
                $casualLeaveBalance = $user->leaveBalance->casual_leave_balance;
                info("User ID: {$user->id}, Current Earned Leave Balance: {$casualLeaveBalance}");
    
                // Increment the user's earned_leave balance by the casual_leave_balance
                $user->leaveBalance->increment('earned_leave_balance', $casualLeaveBalance);
                info("User ID: {$user->id}, Incremented casual_leave_balance by $casualLeaveBalance days");
    
                // Reset the casual_leave_balance to its default value
                $user->leaveBalance->casual_leave_balance = $defaultCasualLeaveBalance;
                $user->leaveBalance->save();
    
                info("User ID: {$user->id}, Casual Leave Balance reset to default: {$defaultCasualLeaveBalance}");
            } else {
                // Log a message for users without leaveBalance
                info("User ID: {$user->id}, Missing Leave Balance for Casual Leave.");
            }
        }
    
        info('Year-end leave balance processing completed.');
    }
    





