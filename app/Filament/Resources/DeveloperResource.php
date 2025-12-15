<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeveloperResource\Pages;
use App\Models\Developer;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeveloperResource extends Resource
{
    protected static ?string $model = Developer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'المشاريع';

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
                Section::make('المعلومات الأساسية')
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2), // Span across 2 columns
                        FileUpload::make('logo')
                            ->label('الشعار')
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
                            ->label('الوصف')
                            ->columnSpan(2), // Span across 2 columns
                    ])
                    ->columns(2), // Use 2 columns for this section

                // Section 2: Contact Information
                Section::make('معلومات الاتصال')
                    ->schema([
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(255)
                            ->columnSpan(1), // Span across 1 column
                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(255)
                            ->columnSpan(1), // Span across 1 column
                        TextInput::make('website')
                            ->label('الموقع الإلكتروني')
                            ->url()
                            ->maxLength(255)
                            ->columnSpan(2), // Span across 2 columns
                    ])
                    ->columns(2), // Use 2 columns for this section

                // Section 3: Address
                Section::make('العنوان')
                    ->schema([
                        Textarea::make('address')
                            ->label('العنوان التفصيلي')
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
                Tables\Columns\ImageColumn::make('logo')->label('الشعار'),
                Tables\Columns\TextColumn::make('email')->label('البريد الإلكتروني'),
                Tables\Columns\TextColumn::make('phone')->label('رقم الهاتف'),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإنشاء')
                    ->dateTime()->sortable(),
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
            'index' => Pages\ListDevelopers::route('/'),
            'create' => Pages\CreateDeveloper::route('/create'),
            'edit' => Pages\EditDeveloper::route('/{record}/edit'),
        ];
    }
}
