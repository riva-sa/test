<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobApplicationResource\Pages;
use App\Models\JobApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Careers';

    protected static ?string $navigationLabel = 'طلبات التوظيف';

    protected static ?string $modelLabel = 'طلب توظيف';

    protected static ?string $pluralModelLabel = 'طلبات التوظيف';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $new = JobApplication::where('status', 'new')->count();

        return $new > 0 ? (string) $new : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    /**
     * Applications are created from the public careers form only.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Review form: candidate data is read-only on the View page; here the
     * admin only manages the pipeline status and internal notes.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('مراجعة الطلب')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('حالة الطلب')
                            ->options(self::statusOptions())
                            ->required(),
                        Forms\Components\Textarea::make('internal_notes')
                            ->label('ملاحظات داخلية')
                            ->rows(6)
                            ->helperText('ملاحظات للفريق فقط — لا تظهر للمتقدم.'),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('بيانات المتقدم')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')->label('الاسم الكامل'),
                                TextEntry::make('email')->label('البريد الإلكتروني')->copyable(),
                                TextEntry::make('phone')->label('رقم الجوال')->copyable(),
                                TextEntry::make('city')->label('المدينة')->placeholder('—'),
                                TextEntry::make('nationality')->label('الجنسية')->placeholder('—'),
                                TextEntry::make('education')->label('المؤهل العلمي')->placeholder('—'),
                                TextEntry::make('years_of_experience')->label('سنوات الخبرة')->placeholder('—'),
                                TextEntry::make('current_job')->label('الوظيفة الحالية')->placeholder('—'),
                                TextEntry::make('current_salary')->label('الراتب الحالي')->placeholder('—'),
                                TextEntry::make('expected_salary')->label('الراتب المتوقع')->placeholder('—'),
                            ]),
                        TextEntry::make('cover_letter')
                            ->label('خطاب التقديم')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ]),

                Section::make('الوظيفة والحالة')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('jobPosting.title')
                                    ->label('الوظيفة')
                                    ->url(fn (JobApplication $record): ?string => $record->jobPosting
                                        ? JobPostingResource::getUrl('edit', ['record' => $record->jobPosting])
                                        : null),
                                TextEntry::make('created_at')
                                    ->label('تاريخ التقديم')
                                    ->dateTime(),
                                TextEntry::make('status')
                                    ->label('حالة الطلب')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => self::statusOptions()[$state] ?? $state)
                                    ->color(fn (string $state): string => self::statusColor($state)),
                            ]),
                        TextEntry::make('internal_notes')
                            ->label('ملاحظات داخلية')
                            ->placeholder('لا توجد ملاحظات')
                            ->columnSpanFull(),
                    ]),

                Section::make('المرفقات')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('cv_path')
                                    ->label('السيرة الذاتية (CV)')
                                    ->formatStateUsing(fn (): string => 'تحميل الملف')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->url(fn (JobApplication $record): string => route('manager.job-application-files.show', ['application' => $record, 'type' => 'cv']))
                                    ->openUrlInNewTab(),
                                TextEntry::make('cover_letter_path')
                                    ->label('خطاب التقديم (ملف)')
                                    ->formatStateUsing(fn (): string => 'تحميل الملف')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->placeholder('لم يرفق')
                                    ->url(fn (JobApplication $record): ?string => $record->cover_letter_path
                                        ? route('manager.job-application-files.show', ['application' => $record, 'type' => 'cover_letter'])
                                        : null)
                                    ->openUrlInNewTab(),
                                TextEntry::make('portfolio_path')
                                    ->label('ملف الأعمال (Portfolio)')
                                    ->formatStateUsing(fn (): string => 'تحميل الملف')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->placeholder('لم يرفق')
                                    ->url(fn (JobApplication $record): ?string => $record->portfolio_path
                                        ? route('manager.job-application-files.show', ['application' => $record, 'type' => 'portfolio'])
                                        : null)
                                    ->openUrlInNewTab(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم المتقدم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الجوال')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jobPosting.title')
                    ->label('الوظيفة')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التقديم')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => self::statusColor($state)),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(self::statusOptions()),
                Tables\Filters\SelectFilter::make('job_posting_id')
                    ->label('الوظيفة')
                    ->relationship('jobPosting', 'title'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('مراجعة'),
                Tables\Actions\EditAction::make()
                    ->label('تغيير الحالة'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->delete();
                        }),
                ]),
            ]);
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            'new' => 'جديد',
            'under_review' => 'قيد المراجعة',
            'shortlisted' => 'قائمة مختصرة',
            'interview_scheduled' => 'مقابلة مجدولة',
            'interviewed' => 'تمت المقابلة',
            'offer_sent' => 'تم إرسال العرض',
            'hired' => 'تم التوظيف',
            'rejected' => 'مرفوض',
            'archived' => 'مؤرشف',
        ];
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            'new' => 'warning',
            'under_review', 'shortlisted', 'interview_scheduled', 'interviewed' => 'info',
            'offer_sent', 'hired' => 'success',
            'rejected' => 'danger',
            default => 'gray',
        };
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobApplications::route('/'),
            'view' => Pages\ViewJobApplication::route('/{record}'),
            'edit' => Pages\EditJobApplication::route('/{record}/edit'),
        ];
    }
}
