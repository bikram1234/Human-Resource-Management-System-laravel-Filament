<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;


class LeaveBalanceStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        // Get the ID of the currently authenticated user
        $userId = auth()->user()->id;

        $currentDate = \Carbon\Carbon::now();
        $formattedDate = $currentDate->format('l d M Y');

        // Fetch leave balance data for the user from the leave_balances table
        $leaveBalanceData = DB::table('leave_balances')
            ->where('employee_id', $userId)
            ->first();

        // Default values
        $earnedLeaveBalance = 0;
        $casualLeaveBalance = 0;

        // If leave balance data exists, update the default values
        if (!is_null($leaveBalanceData)) {
            $earnedLeaveBalance = $leaveBalanceData->earned_leave_balance;
            $casualLeaveBalance = $leaveBalanceData->casual_leave_balance;
        }


        return [
            Card::make('Earned Leave Balance', $earnedLeaveBalance)
                ->color('#FF6384') // Red color for earned leave balance
                ->icon('heroicon-o-clock'),
            Card::make('Casual Leave Balance', $casualLeaveBalance)
                ->color('#36A2EB') // Blue color for casual leave balance
                ->icon('heroicon-o-calendar'),
            Card::make('Current Date', $formattedDate)
            ->color('#FF6384') // Red color for current date
            ->icon('heroicon-o-calendar'),
        ];
    }
}
