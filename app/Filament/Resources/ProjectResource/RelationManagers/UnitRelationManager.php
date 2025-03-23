<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Components\Actions\Action;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\Group;
use Filament\Forms\Set;
use App\Models\Unit;

class UnitRelationManager extends RelationManager
{
    protected static string $relationship = 'Units';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Basic Information Section
                Forms\Components\Section::make('Basic Information')
                ->schema([
                    // title

                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $state, Forms\Set $set) {
                            $set('slug', Str::slug($state));
                        }),

                    Forms\Components\TextInput::make('slug')
                        ->nullable()
                        ->maxLength(255)
                        ->unique(Unit::class, 'slug', ignoreRecord: true)
                        ->columnSpan(1)
                        ->disabled()
                        ->dehydrated(fn ($state) => filled($state)),

                    Forms\Components\TextInput::make('unit_type')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('building_number')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('unit_number')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),
                    // sale_type
                    // Forms\Components\Select::make('sale_type')
                    //     ->options([
                    //         'direct' => 'direct',
                    //         'installment' => 'Installment',
                    //     ])
                    //     ->required()
                    //     ->columnSpan(1),
                    Forms\Components\TextInput::make('floor')
                        ->required()
                        ->numeric()
                        ->columnSpan(1),
                    Forms\Components\RichEditor::make('description')
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(3),

            // Unit Specifications Section
            Forms\Components\Section::make('Unit Specifications')
                ->schema([
                    Forms\Components\TextInput::make('unit_area')
                        ->required()
                        ->numeric()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('unit_price')
                        ->required()
                        ->numeric()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('living_rooms')
                        ->required()
                        ->numeric()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('beadrooms')
                        ->required()
                        ->numeric()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('bathrooms')
                        ->required()
                        ->numeric()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('kitchen')
                        ->required()
                        ->numeric()
                        ->columnSpan(1)
                ])
                ->columns(3),

            // Status Section
            Forms\Components\Section::make('Status')
                ->schema([
                    Forms\Components\Toggle::make('show_price')
                        ->offColor('danger')
                        ->onColor('success')
                        ->default(true)
                        ->columnSpan(1),
                    Forms\Components\Toggle::make('status')
                        ->offColor('danger')
                        ->onColor('success')
                        ->default(true)
                        ->columnSpan(1)
                ])
                ->columns(2),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('slug')
            ->columns([
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('title'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
