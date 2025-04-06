<?php

namespace Firefly\FilamentBlog\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Firefly\FilamentBlog\Models\Setting;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'المدونة';

    protected static ?string $navigationLabel = 'اعدادات المدونة';

    protected static ?int $navigationSort = 8;

    //
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Setting::getForm());
    }

    public static function canCreate(): bool
    {
        return Setting::count() === 0;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->limit(25)
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\ImageColumn::make('logo')
                    ->label('الشعار'),

                Tables\Columns\TextColumn::make('organization_name')
                    ->label('اسم المؤسسة'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\ViewAction::make()->label('عرض'),
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
            'index' => \Firefly\FilamentBlog\Resources\SettingResource\Pages\ListSettings::route('/'),
            'create' => \Firefly\FilamentBlog\Resources\SettingResource\Pages\CreateSetting::route('/create'),
            'edit' => \Firefly\FilamentBlog\Resources\SettingResource\Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
