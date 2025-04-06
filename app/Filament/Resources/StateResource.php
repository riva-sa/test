<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StateResource\Pages;
use App\Filament\Resources\StateResource\RelationManagers;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StateResource extends Resource
{
    protected static ?string $model = State::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'المواقع';
    protected static ?string $navigationLabel = 'الحي';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('country')
                            ->label('الدولة')
                            ->required()
                            ->maxLength(255)
                            ->default('SA'),
                        Forms\Components\FileUpload::make('photo')
                            ->label('الصورة')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->disk('public')
                            ->directory('states/photo'),

                        Forms\Components\Toggle::make('status')
                            ->label('الحالة')
                            ->onColor('success')
                            ->offColor('danger')
                            ->required(),
                        Forms\Components\Select::make('city_id')
                            ->label('المدينة')
                            ->preload()
                            ->native(false)
                            ->relationship('city', 'name')
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->label('الدولة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('المدينة')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('الحالة')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city_id')
                    ->label('المدينة')
                    ->relationship('city', 'name')
                    ->preload()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('country')
                    ->label('الدولة')
                    ->options([
                        'SA' => 'المملكة العربية السعودية',
                    ])
                ->placeholder('اختر الدولة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStates::route('/'),
            'create' => Pages\CreateState::route('/create'),
            'edit' => Pages\EditState::route('/{record}/edit'),
        ];
    }
}
