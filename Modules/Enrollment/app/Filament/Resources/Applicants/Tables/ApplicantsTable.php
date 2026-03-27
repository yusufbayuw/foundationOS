<?php

namespace Modules\Enrollment\Filament\Resources\Applicants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Core\Filament\Support\ImportTableActions;

class ApplicantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('admissionPeriod.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('admissionPeriod.name'))
                    ->searchable(),
                TextColumn::make('registration_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('registration_number'))
                    ->searchable(),
                TextColumn::make('full_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('full_name'))
                    ->searchable(),
                TextColumn::make('birth_place')
                    ->label(\Modules\Core\Support\FilamentUi::field('birth_place'))
                    ->searchable(),
                TextColumn::make('birth_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('birth_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('gender')
                    ->label(\Modules\Core\Support\FilamentUi::field('gender'))
                    ->searchable(),
                TextColumn::make('religion')
                    ->label(\Modules\Core\Support\FilamentUi::field('religion'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                    ->searchable(),
                TextColumn::make('parent_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('parent_name'))
                    ->searchable(),
                TextColumn::make('parent_phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('parent_phone'))
                    ->searchable(),
                TextColumn::make('previous_school')
                    ->label(\Modules\Core\Support\FilamentUi::field('previous_school'))
                    ->searchable(),
                TextColumn::make('nisn')
                    ->label(\Modules\Core\Support\FilamentUi::field('nisn'))
                    ->searchable(),
                TextColumn::make('ijazah_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('ijazah_number'))
                    ->searchable(),
                TextColumn::make('average_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('average_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('achievement_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('achievement_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('program_choice_1_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('program_choice_1_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('program_choice_2_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('program_choice_2_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('test_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('test_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('interview_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('interview_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('final_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('final_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ranking')
                    ->label(\Modules\Core\Support\FilamentUi::field('ranking'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_passed')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_passed'))
                    ->boolean(),
                TextColumn::make('acceptedProgram.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('acceptedProgram.name'))
                    ->searchable(),
                TextColumn::make('enrollment_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('enrollment_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('converted_to_student_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('converted_to_student_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('photo')
                    ->label(\Modules\Core\Support\FilamentUi::field('photo'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(\App\Filament\Imports\ApplicantImporter::class),
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
