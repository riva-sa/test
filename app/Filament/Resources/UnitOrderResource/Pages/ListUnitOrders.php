<?php

namespace App\Filament\Resources\UnitOrderResource\Pages;

use App\Filament\Resources\UnitOrderResource;
use App\Filament\Resources\UnitOrderResource\Widgets\UnitOrderStats;
use App\Jobs\ExportOrdersJob;
use App\Models\OrderExport;
use Filament\Actions;
use Filament\Notifications\Notification;
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
            Actions\Action::make('exportAll')
                ->label('تصدير الكل')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    // Prevent duplicate exports
                    $existing = OrderExport::where('user_id', auth()->id())
                        ->whereIn('status', ['pending', 'processing'])
                        ->first();

                    if ($existing) {
                        Notification::make()
                            ->title('يوجد تصدير جاري بالفعل')
                            ->body('انتظر حتى يكتمل التصدير الحالي.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $fileName = 'all_orders_' . now()->format('Y-m-d_His') . '.csv';

                    $export = OrderExport::create([
                        'user_id' => auth()->id(),
                        'file_name' => $fileName,
                        'filters' => [],
                        'status' => 'pending',
                    ]);

                    ExportOrdersJob::dispatch($export->id, auth()->id(), []);

                    Notification::make()
                        ->title('بدأ تصدير جميع الطلبات')
                        ->body('سيتم إشعارك عند اكتمال التصدير مع رابط التحميل. يمكنك متابعة العمل.')
                        ->info()
                        ->send();
                }),
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
