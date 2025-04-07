<?php

namespace App\Filament\Resources\UnitOrderResource\Pages;

use App\Filament\Resources\UnitOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
// use App\Filament\Resources\UnitOrderResource\Widgets\ProjectsUnitsOrders;
use App\Filament\Resources\UnitOrderResource\Widgets\UnitOrderStats;
class ListUnitOrders extends ListRecords
{
    protected static string $resource = UnitOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('viewUserOrders')
            ->label('عرض طلبات المستخدمين')
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
