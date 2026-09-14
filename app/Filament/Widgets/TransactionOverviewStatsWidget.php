<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class TransactionOverviewStatsWidget extends StatsOverviewWidget
{
    use HasWidgetShield;

    protected ?string $pollingInterval = null;

    // four cards wide layout for the widget header area
    protected function getColumns(): int|array
    {
        return 4;
    }

    protected function getStats(): array
    {
        if (app()->isLocal()) {
            return $this->buildStats();
        }

        return cache()->remember('filament.transactions.stats.widget', now()->addMinutes(10), fn (): array => $this->buildStats());
    }

    protected function buildStats(): array
    {
        $returnVisits = $this->countReturnVisits();

        // Total transaction
        $paymentAttempts = Transaction::count();
        $totalRevenue = Transaction::sum('amount') / 100;

        // Successful transaction
        $successfulPaymentAttempts = Transaction::where('status', 'successful')->count();
        $successfulTotalRevenue = Transaction::where('status', 'successful')->sum('amount') / 100;

        // Pending transaction
        $pendingPaymentAttempts = Transaction::where('status', 'pending')->count();
        $paymentTotalRevenue = Transaction::where('status', 'pending')->sum('amount') / 100;

        return [
            Stat::make('Return Visits', Number::abbreviate($returnVisits))
                ->description('Users who returned after their first successful login')
                ->icon('heroicon-m-arrow-uturn-left'),

            // All transactions
            // number_format() to Number::abbreviate()
            Stat::make('Payment Attempts', Number::abbreviate($paymentAttempts))
                ->description('Transaction attempts recorded across companies')
                ->color('primary')
                ->icon('heroicon-m-credit-card'),

            Stat::make('Total Revenue', '₦'.Number::abbreviate($totalRevenue, 2))
                ->description('All transaction volume across companies')
                ->color('primary')
                ->icon('heroicon-m-banknotes'),

            // Pending transactions
            Stat::make('Pending Payment Attempts', Number::abbreviate($pendingPaymentAttempts))
                ->description('Transaction pending attempts recorded across companies')
                ->color('warning')
                ->icon('heroicon-m-credit-card'),

            Stat::make('Total Revenue (Pending)', '₦'.Number::abbreviate($paymentTotalRevenue, 2))
                ->description('All pending transaction volume across companies')
                ->color('warning')
                ->icon('heroicon-m-banknotes'),

            // Successful transactions
            Stat::make('Successful Payment Attempts', Number::abbreviate($successfulPaymentAttempts))
                ->description('Transaction successful attempts recorded across companies')
                ->color('success')
                ->icon('heroicon-m-credit-card'),

            Stat::make('Total Revenue (Successful)', '₦'.Number::abbreviate($successfulTotalRevenue, 2))
                ->description('All successful transaction volume across companies')
                ->color('success')
                ->icon('heroicon-m-banknotes'),
        ];
    }

    protected function countReturnVisits(): int
    {
        $tableName = config('authentication-log.table_name', 'authentication_log');

        $records = DB::table($tableName)
            ->select('authenticatable_id')
            ->where('login_successful', true)
            ->groupBy('authenticatable_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        return $records->count();
    }
}
