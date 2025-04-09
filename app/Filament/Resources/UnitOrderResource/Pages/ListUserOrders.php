<?php

namespace App\Filament\Resources\UnitOrderResource\Pages;

use App\Filament\Resources\UnitOrderResource;
use Filament\Resources\Pages\Page;
use App\Models\UnitOrder;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\HtmlString;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;

class ListUserOrders extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = UnitOrderResource::class;
    protected static string $view = 'filament.resources.unit-order-resource.pages.list-user-orders';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // UnitOrder::query()
                //     ->select('phone')
                //     ->selectRaw('MIN(id) as id')
                //     ->selectRaw('GROUP_CONCAT(DISTINCT name SEPARATOR "||") as names')
                //     ->selectRaw('GROUP_CONCAT(DISTINCT email SEPARATOR "||") as emails')
                //     ->selectRaw('COUNT(*) as total_orders')
                //     ->selectRaw('MAX(created_at) as last_order')
                //     ->groupBy('phone')
                UnitOrder::query()
                    ->select('phone')
                    ->selectRaw('MIN(id) as id')
                    ->selectRaw('COUNT(*) as total_orders')
                    ->selectRaw('MAX(created_at) as last_order')
                    ->groupBy('phone')
            )
            ->columns([
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->extraAttributes(['class' => 'font-medium']),



                Tables\Columns\TextColumn::make('total_orders')
                    ->label('عدد الطلبات')
                    ->sortable()
                    ->badge()
                    ->color(fn($state) =>
                        $state >= 5 ? 'success' :
                        ($state >= 3 ? 'warning' : 'gray')
                    )
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('PurchaseType')
                    ->label('نوع الشراء'),

                Tables\Columns\TextColumn::make('last_order')
                    ->label('آخر طلب')
                    ->dateTime('d M Y - H:i')
                    ->sortable()
                    ->color('primary')
                    ->icon('heroicon-o-calendar')
            ])
            ->defaultSort('last_order', 'desc')
            ->actions([
                Tables\Actions\Action::make('view_orders')
                    ->label('عرض الطلبات')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->button()
                    ->url(fn ($record) => route('filament.admin.resources.unit-orders.index', [
                        'tableSearch' => $record->phone
                    ]))
            ])
            ->filters([
                // Tables\Filters\Filter::make('high_value_customers')
                //     ->label('العملاء ذوي القيمة العالية')
                //     ->query(fn (Builder $query) => $query->having('total_spent', '>=', 1000)),

                Tables\Filters\Filter::make('repeat_customers')
                    ->label('العملاء المتكررين')
                    ->query(fn (Builder $query) => $query->having('total_orders', '>=', 3)),

                Tables\Filters\Filter::make('recent_customers')
                    ->label('العملاء الجدد')
                    ->query(fn (Builder $query) => $query->whereRaw('last_order >= DATE_SUB(NOW(), INTERVAL 7 DAY)'))
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('export_selected')
                    ->label('تصدير المحدد')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function ($records) {
                        // Export logic here
                    })
            ]);
    }
}
