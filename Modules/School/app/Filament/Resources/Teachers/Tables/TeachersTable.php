<?php

namespace Modules\School\Filament\Resources\Teachers\Tables;

use App\Filament\Imports\TeacherImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class TeachersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(FilamentUi::field('user.name'))
                    ->searchable(),
                TextColumn::make('nip')
                    ->label(FilamentUi::field('nip'))
                    ->searchable(),
                TextColumn::make('nuptk')
                    ->label(FilamentUi::field('nuptk'))
                    ->searchable(),
                TextColumn::make('nrg')
                    ->label(FilamentUi::field('nrg'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('employment_status')
                    ->label(FilamentUi::field('employment_status'))
                    ->searchable(),
                TextColumn::make('join_date')
                    ->label(FilamentUi::field('join_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('resignation_date')
                    ->label(FilamentUi::field('resignation_date'))
                    ->date()
                    ->sortable(),
                IconColumn::make('is_certified')
                    ->label(FilamentUi::field('is_certified'))
                    ->boolean(),
                TextColumn::make('certification_year')
                    ->label(FilamentUi::field('certification_year'))
                    ->searchable(),
                TextColumn::make('certification_number')
                    ->label(FilamentUi::field('certification_number'))
                    ->searchable(),
                TextColumn::make('highest_education')
                    ->label(FilamentUi::field('highest_education'))
                    ->searchable(),
                TextColumn::make('major_study')
                    ->label(FilamentUi::field('major_study'))
                    ->searchable(),
                TextColumn::make('university')
                    ->label(FilamentUi::field('university'))
                    ->searchable(),
                TextColumn::make('functional_position')
                    ->label(FilamentUi::field('functional_position'))
                    ->searchable(),
                TextColumn::make('structural_position')
                    ->label(FilamentUi::field('structural_position'))
                    ->searchable(),
                TextColumn::make('teaching_hours_per_week')
                    ->label(FilamentUi::field('teaching_hours_per_week'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('base_salary')
                    ->label(FilamentUi::field('base_salary'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('allowance')
                    ->label(FilamentUi::field('allowance'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bpjs_tk_number')
                    ->label(FilamentUi::field('bpjs_tk_number'))
                    ->searchable(),
                TextColumn::make('bpjs_kes_number')
                    ->label(FilamentUi::field('bpjs_kes_number'))
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
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(TeacherImporter::class),
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
