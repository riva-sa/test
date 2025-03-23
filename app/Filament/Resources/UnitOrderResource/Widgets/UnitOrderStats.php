<?php

namespace App\Filament\Resources\UnitOrderResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\UnitOrder;
use Illuminate\Support\Facades\Auth;

class UnitOrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        // Check if the current user is a sales_manager
        $user = Auth::user();
        $query = UnitOrder::query();

        // If the user has the 'sales_manager' role, filter by sales_manager_id
        if ($user->hasRole('sales_manager')) {
            $query->whereHas('project', function ($q) use ($user) {
                $q->where('sales_manager_id', $user->id); // Assuming UnitOrder belongs to a Project
            });
        }

        return [
            Stat::make('طلبيات الوحدات الجديدة', $query->where('status', 0)->count())
                ->chart([
                    $query->where('status', 0)->whereDate('created_at', '>', now()->subDays(7))->count(),
                    $query->where('status', 0)->whereDate('created_at', '>', now()->subDays(14))->count(),
                    $query->where('status', 0)->whereDate('created_at', '>', now()->subDays(21))->count(),
                ])
                ->description('Last 7, 14, 21 days')
                ->color('success'),

            Stat::make('طلبيات الوحدات (تم التواصل معهم)', $query->where('status', '1')->count())
                ->chart([
                    $query->where('status', '1')->whereDate('updated_at', '>', now()->subDays(7))->count(),
                    $query->where('status', '1')->whereDate('updated_at', '>', now()->subDays(14))->count(),
                    $query->where('status', '1')->whereDate('updated_at', '>', now()->subDays(21))->count(),
                ])
                ->description('Last 7, 14, 21 days')
                ->color('warning'),

            Stat::make('طلبيات الوحدات المباعة', $query->where('status', '2')->count())
                ->chart([
                    $query->where('status', '2')->whereDate('updated_at', '>', now()->subDays(7))->count(),
                    $query->where('status', '2')->whereDate('updated_at', '>', now()->subDays(14))->count(),
                    $query->where('status', '2')->whereDate('updated_at', '>', now()->subDays(21))->count(),
                ])
                ->description('Last 7, 14, 21 days')
                ->color('info'),

        ];
    }
}
