<?php

namespace Firefly\FilamentBlog\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Firefly\FilamentBlog\Models\Comment;
use Firefly\FilamentBlog\Tables\Columns\UserPhotoName;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'المدونة';

    protected static ?string $navigationLabel = 'التعليقات';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Comment::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                UserPhotoName::make('user')
                    ->label('المستخدم'),
                Tables\Columns\TextColumn::make('post.title')
                    ->label('المقال')
                    ->numeric()
                    ->limit(20)
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label('التعليق')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\ToggleColumn::make('approved')
                    ->label('معتمد')
                    ->beforeStateUpdated(function ($record, $state) {
                        if ($state) {
                            $record->approved_at = now();
                        } else {
                            $record->approved_at = null;
                        }

                        return $state;
                    }),
                Tables\Columns\TextColumn::make('approved_at')
                    ->label('تاريخ الاعتماد')
                    ->sortable()
                    ->placeholder('لم يتم الاعتماد بعد'),

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
                Tables\Filters\SelectFilter::make('user')
                    ->label('المستخدم')
                    ->relationship('user', config('filamentblog.user.columns.name'))
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()->label('تعديل'),
                    Tables\Actions\DeleteAction::make()->label('حذف'),
                    Tables\Actions\ViewAction::make()->label('عرض'),
                ]),
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
            'index' => \Firefly\FilamentBlog\Resources\CommentResource\Pages\ListComments::route('/'),
            'create' => \Firefly\FilamentBlog\Resources\CommentResource\Pages\CreateComment::route('/create'),
            'edit' => \Firefly\FilamentBlog\Resources\CommentResource\Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
