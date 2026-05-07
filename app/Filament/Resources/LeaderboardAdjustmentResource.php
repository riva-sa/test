<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaderboardAdjustmentResource\Pages;
use App\Filament\Resources\LeaderboardAdjustmentResource\RelationManagers;
use App\Models\LeaderboardAdjustment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaderboardAdjustmentResource extends Resource
{
    protected static ?string $model = LeaderboardAdjustment::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    public static function getNavigationLabel(): string
    {
        return __('leaderboard.adjust_points');
    }

    public static function getPluralLabel(): string
    {
        return __('leaderboard.adjust_points');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('leaderboard.employee'))
                            ->relationship('agent', 'name', fn (Builder $query) => $query->role('sales'))
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('period_type')
                            ->label(__('leaderboard.period_type'))
                            ->options([
                                'daily' => __('leaderboard.daily'),
                                'weekly' => __('leaderboard.weekly'),
                                'monthly' => __('leaderboard.monthly'),
                            ])
                            ->required()
                            ->default('daily'),

                        Forms\Components\DatePicker::make('period_date')
                            ->label(__('leaderboard.period_date'))
                            ->required()
                            ->default(now()),

                        Forms\Components\Select::make('metric_type')
                            ->label(__('leaderboard.metric'))
                            ->options([
                                'monthly_orders' => 'Monthly Orders',
                                'daily_orders' => 'Daily Orders',
                                'reservations' => 'Sales Transactions',
                                'sales' => 'Completed Sales',
                                'composite_score' => 'Composite Score',
                            ])
                            ->required()
                            ->default('composite_score'),

                        Forms\Components\TextInput::make('original_value')
                            ->label(__('leaderboard.original_value'))
                            ->numeric()
                            ->default(0)
                            ->disabled(),

                        Forms\Components\TextInput::make('adjusted_value')
                            ->label(__('leaderboard.new_value'))
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->helperText(__('leaderboard.no_negative_values')),

                        Forms\Components\Textarea::make('reason')
                            ->label(__('leaderboard.reason'))
                            ->placeholder(__('leaderboard.reason_placeholder'))
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('adjusted_by')
                            ->default(auth()->id()),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agent.name')
                    ->label(__('leaderboard.employee'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('period_type')
                    ->label(__('leaderboard.period_type'))
                    ->badge(),

                Tables\Columns\TextColumn::make('period_date')
                    ->label(__('leaderboard.period_date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('metric_type')
                    ->label(__('leaderboard.metric'))
                    ->badge(),

                Tables\Columns\TextColumn::make('original_value')
                    ->label(__('leaderboard.original_value'))
                    ->numeric(2),

                Tables\Columns\TextColumn::make('adjusted_value')
                    ->label(__('leaderboard.new_value'))
                    ->numeric(2)
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('admin.name')
                    ->label('بواسطة')
                    ->description(fn (LeaderboardAdjustment $record): string => $record->created_at->diffForHumans()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label(__('leaderboard.employee'))
                    ->relationship('agent', 'name'),
                Tables\Filters\SelectFilter::make('period_type')
                    ->label(__('leaderboard.period_type'))
                    ->options([
                        'daily' => __('leaderboard.daily'),
                        'weekly' => __('leaderboard.weekly'),
                        'monthly' => __('leaderboard.monthly'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLeaderboardAdjustments::route('/'),
        ];
    }
}
