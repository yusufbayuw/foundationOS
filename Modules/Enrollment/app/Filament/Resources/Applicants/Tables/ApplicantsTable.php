<?php

namespace Modules\Enrollment\Filament\Resources\Applicants\Tables;

use App\Filament\Imports\ApplicantImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class ApplicantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['tenant', 'admissionPeriod']))
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('admissionPeriod.name')
                    ->label(FilamentUi::field('admissionPeriod.name'))
                    ->searchable(),
                TextColumn::make('registration_number')
                    ->label(FilamentUi::field('registration_number'))
                    ->searchable(),
                TextColumn::make('full_name')
                    ->label(FilamentUi::field('full_name'))
                    ->searchable(),
                TextColumn::make('birth_place')
                    ->label(FilamentUi::field('birth_place'))
                    ->searchable(),
                TextColumn::make('birth_date')
                    ->label(FilamentUi::field('birth_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('gender')
                    ->label(FilamentUi::field('gender'))
                    ->searchable(),
                TextColumn::make('religion')
                    ->label(FilamentUi::field('religion'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(FilamentUi::field('phone'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(FilamentUi::text('Email address'))
                    ->searchable(),
                TextColumn::make('parent_name')
                    ->label(FilamentUi::field('parent_name'))
                    ->searchable(),
                TextColumn::make('parent_phone')
                    ->label(FilamentUi::field('parent_phone'))
                    ->searchable(),
                TextColumn::make('previous_school')
                    ->label(FilamentUi::field('previous_school'))
                    ->searchable(),
                TextColumn::make('nisn')
                    ->label(FilamentUi::field('nisn'))
                    ->searchable(),
                TextColumn::make('ijazah_number')
                    ->label(FilamentUi::field('ijazah_number'))
                    ->searchable(),
                TextColumn::make('average_score')
                    ->label(FilamentUi::field('average_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('achievement_count')
                    ->label(FilamentUi::field('achievement_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('program_choice_1_id')
                    ->label(FilamentUi::field('program_choice_1_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('program_choice_2_id')
                    ->label(FilamentUi::field('program_choice_2_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('test_score')
                    ->label(FilamentUi::field('test_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('interview_score')
                    ->label(FilamentUi::field('interview_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('final_score')
                    ->label(FilamentUi::field('final_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ranking')
                    ->label(FilamentUi::field('ranking'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_passed')
                    ->label(FilamentUi::field('is_passed'))
                    ->boolean(),
                TextColumn::make('acceptedProgram.name')
                    ->label(FilamentUi::field('acceptedProgram.name'))
                    ->searchable(),
                TextColumn::make('enrollment_date')
                    ->label(FilamentUi::field('enrollment_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('converted_to_student_id')
                    ->label(FilamentUi::field('converted_to_student_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('photo')
                    ->label(FilamentUi::field('photo'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options([
                        'registered' => 'Registered',
                        'screening' => 'Screening',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'enrolled' => 'Enrolled',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(ApplicantImporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
