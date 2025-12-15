<?php

namespace App\Filament\Resources\UnitOrderResource\Pages;

use App\Filament\Resources\UnitOrderResource;
use App\Filament\Resources\UnitOrderResource\Widgets\UnitOrderStats;
use Filament\Actions;
// use App\Filament\Resources\UnitOrderResource\Widgets\ProjectsUnitsOrders;
use Filament\Resources\Pages\ListRecords;

class ListUnitOrders extends ListRecords
{
    protected static string $resource = UnitOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('viewUserOrders')
                ->label('عرض العملاء مع طلباتهم')
                ->icon('heroicon-o-users')
                ->color('secondary')
                ->url(fn () => static::getResource()::getUrl('users')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // ProjectsUnitsOrders::class,
        ];
    }

    public static function widgets(): array
    {
        return [
            UnitOrderStats::class,
        ];
    }
}
