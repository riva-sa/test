<?php

namespace App\Filament\Widgets;

use App\Models\UnitOrder;
use App\Filament\Resources\UnitOrderResource;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Notifications\Notification;

class LatestUnitOrders extends BaseWidget
{
    protected int | string | array $columnSpan = 'full'; // Full-width widget

    protected static ?int $sort = 2; // Sort order in dashboard widgets

    protected static ?string $heading = 'أحدث طلبات الوحدات';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(UnitOrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5) // Default pagination with 5 orders per page
            ->defaultSort('created_at', 'desc') // Sort by latest created_at
            ->columns([
                Tables\Columns\TextColumn::make('user_info')
                    ->label('المعلومات')
                    ->getStateUsing(function ($record) {
                        return "<strong>{$record->name}</strong><br>{$record->email}<br>{$record->phone}";
                    })
                    ->html() // Enable HTML rendering for the column
                    ->sortable(['name', 'email', 'phone']) // Make it sortable
                    ->searchable(['name', 'email', 'phone']),

                Tables\Columns\SelectColumn::make('status')
                    ->label('الحالة')
                    ->options([
                        0 => 'جديد',
                        1 => 'قيد المعالجة',
                        2 => 'مكتمل',
                        3 => 'ملغي',
                    ])
                    ->default(fn ($record) => $record->status) // Set the default value based on the current status
                    ->searchable()
                    ->afterStateUpdated(function ($state) {
                        // Show a success notification after state update
                        Notification::make()
                            ->title('تم التحديث')
                            ->body('تم تحديث الحالة بنجاح')
                            ->success()
                            ->send();
                    }),
                Tables\Columns\TextColumn::make('message')->label('الرسالة')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('PurchaseType')->label('طريقة الشراء')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('PurchasePurpose')->label('الغرض من الشراء')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit.title')->label('الوحدة')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('العميل')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')->label('المشروع')
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
            ]);
    }
}
