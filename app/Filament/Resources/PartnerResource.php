<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerResource\Pages;
use App\Filament\Resources\PartnerResource\RelationManagers;
use App\Models\Partner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'شركاء النجاح';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // name
                Forms\Components\TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                // logo
                Forms\Components\FileUpload::make('logo')
                    ->label('الشعار')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->directory('partners')
                    ->columnSpan(2),
                // status
                Forms\Components\Toggle::make('status')
                    ->label('الحالة')
                    ->onColor('success')
                    ->required()
                    ->default(true)
                    ->columnSpan(1)
                    ->offColor('danger'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')->label('الشعار'),
                Tables\Columns\TextColumn::make('name')->label('الاسم'),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('الحالة')
                    ->onColor('success')
                    ->offColor('danger')
            ])
            ->filters([
                //
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
            'index' => Pages\ListPartners::route('/'),
            // 'create' => Pages\CreatePartner::route('/create'),
            // 'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
