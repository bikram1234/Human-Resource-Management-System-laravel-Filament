<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Carbon\Carbon;

class changeEmployementType extends Command{
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Employee:employement-type-process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Employeement-Type processing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = FilamentUser::with('leaveBalance')->where('employment_type', 'probation_period')->get();

        foreach ($users as $user) {
            $appointmentDate = Carbon::parse($user->date_of_appointment);
            $sixMonthsAgo = now()->subMonths(6);

            if ($appointmentDate->lte($sixMonthsAgo)) {
                // Update employment type to 'regular_period'
                $user->update(['employment_type' => 'regular_period']);
                
                info("Updated employment type for user {$user->id}: {$user->name}");
            } else {
                info("No update needed for user {$user->id}: {$user->name}");
            }
        }
    }

}
