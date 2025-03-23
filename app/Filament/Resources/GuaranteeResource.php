<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuaranteeResource\Pages;
use App\Filament\Resources\GuaranteeResource\RelationManagers;
use App\Models\Guarantee;
use Doctrine\DBAL\Query\From;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Project;
use Filament\Forms\Components\CheckboxList;

class GuaranteeResource extends Resource
{
    protected static ?string $model = Guarantee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'المضمانات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('icon')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->disk('public')
                            ->directory('garantee/images'),
                        Forms\Components\Toggle::make('is_active')->default(true),

                        CheckboxList::make('projects')
                            ->label('Projects')
                            ->relationship('projects', 'name') // Uses the relationship
                            ->columns(4)
                            ->options(Project::all()->pluck('name', 'id')->toArray()), // Fetch available projects from the database

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم'),
                Tables\Columns\ToggleColumn::make('is_active')->label('الحالة')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListGuarantees::route('/'),
            'create' => Pages\CreateGuarantee::route('/create'),
            'edit' => Pages\EditGuarantee::route('/{record}/edit'),
        ];
    }
}
