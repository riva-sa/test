<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'رسائل الاتصال';

    protected static ?string $pluralLabel = 'رسائل الاتصال';

    protected static ?string $modelLabel = 'رسالة اتصال';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('الاسم')
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->required(),

                Forms\Components\Select::make('department')
                    ->label('القسم')
                    ->options([
                        'Sales' => 'مبيعات',
                        'Marketing' => 'تسويق',
                        'Customer Support' => 'خدمة عملاء',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('message')
                    ->label('الرسالة')
                    ->required()
                    ->rows(4),

                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options([
                        'new' => 'جديد',
                        'processing' => 'قيد المعالجة',
                        'completed' => 'تم الرد',
                        'archived' => 'مؤرشف',
                    ])
                    ->default('new')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),

                Tables\Columns\TextColumn::make('department')
                    ->label('القسم'),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'archived' => 'gray',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (Contact $record) => match ($record->status) {
                        'new' => 'جديد',
                        'processing' => 'قيد المعالجة',
                        'completed' => 'تم الرد',
                        'archived' => 'مؤرشف',
                        default => 'جديد',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإرسال')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->label('القسم')
                    ->options([
                        'Sales' => 'مبيعات',
                        'Marketing' => 'تسويق',
                        'Customer Support' => 'خدمة عملاء',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'new' => 'جديد',
                        'processing' => 'قيد المعالجة',
                        'completed' => 'تم الرد',
                        'archived' => 'مؤرشف',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('تعديل'),
                Tables\Actions\DeleteAction::make()
                    ->label('حذف'),
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
