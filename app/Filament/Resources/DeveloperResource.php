<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeveloperResource\Pages;
use App\Filament\Resources\DeveloperResource\RelationManagers;
use App\Models\Developer;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\RichEditor;

class DeveloperResource extends Resource
{
    protected static ?string $model = Developer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Projects';

    protected static ?string $navigationLabel = 'المطورين';

    protected static ?int $navigationSort = 5;

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'description',
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Section 1: Basic Information
                Section::make('Basic Information')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2), // Span across 2 columns
                    FileUpload::make('logo')
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios([
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                        ->directory('developers')
                        ->columnSpan(2), // Span across 2 columns
                    RichEditor::make('description')
                        ->columnSpan(2), // Span across 2 columns
                ])
                ->columns(2), // Use 2 columns for this section

                // Section 2: Contact Information
                Section::make('Contact Information')
                ->schema([
                    TextInput::make('email')
                        ->email()
                        ->maxLength(255)
                        ->columnSpan(1), // Span across 1 column
                    TextInput::make('phone')
                        ->tel()
                        ->maxLength(255)
                        ->columnSpan(1), // Span across 1 column
                    TextInput::make('website')
                        ->url()
                        ->maxLength(255)
                        ->columnSpan(2), // Span across 2 columns
                ])
                ->columns(2), // Use 2 columns for this section

                // Section 3: Address
                Section::make('Address')
                ->schema([
                    Textarea::make('address')
                        ->maxLength(65535)
                        ->columnSpan(2), // Span across 2 columns
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
                Tables\Columns\ImageColumn::make('logo')->label('اللوجو'),
                Tables\Columns\TextColumn::make('email')->label('البريد'),
                Tables\Columns\TextColumn::make('phone')->label('الهاتف'),
                Tables\Columns\TextColumn::make('created_at')->label('التاريخ')
                    ->dateTime()->sortable(),
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
            'index' => Pages\ListDevelopers::route('/'),
            'create' => Pages\CreateDeveloper::route('/create'),
            'edit' => Pages\EditDeveloper::route('/{record}/edit'),
        ];
    }
}
