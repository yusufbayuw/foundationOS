<?php

namespace Modules\School\Filament\Resources\Teachers\Tables;

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

class TeachersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('user.name'))
                    ->searchable(),
                TextColumn::make('nip')
                    ->label(\Modules\Core\Support\FilamentUi::field('nip'))
                    ->searchable(),
                TextColumn::make('nuptk')
                    ->label(\Modules\Core\Support\FilamentUi::field('nuptk'))
                    ->searchable(),
                TextColumn::make('nrg')
                    ->label(\Modules\Core\Support\FilamentUi::field('nrg'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('employment_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('employment_status'))
                    ->searchable(),
                TextColumn::make('join_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('join_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('resignation_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('resignation_date'))
                    ->date()
                    ->sortable(),
                IconColumn::make('is_certified')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_certified'))
                    ->boolean(),
                TextColumn::make('certification_year')
                    ->label(\Modules\Core\Support\FilamentUi::field('certification_year'))
                    ->searchable(),
                TextColumn::make('certification_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('certification_number'))
                    ->searchable(),
                TextColumn::make('highest_education')
                    ->label(\Modules\Core\Support\FilamentUi::field('highest_education'))
                    ->searchable(),
                TextColumn::make('major_study')
                    ->label(\Modules\Core\Support\FilamentUi::field('major_study'))
                    ->searchable(),
                TextColumn::make('university')
                    ->label(\Modules\Core\Support\FilamentUi::field('university'))
                    ->searchable(),
                TextColumn::make('functional_position')
                    ->label(\Modules\Core\Support\FilamentUi::field('functional_position'))
                    ->searchable(),
                TextColumn::make('structural_position')
                    ->label(\Modules\Core\Support\FilamentUi::field('structural_position'))
                    ->searchable(),
                TextColumn::make('teaching_hours_per_week')
                    ->label(\Modules\Core\Support\FilamentUi::field('teaching_hours_per_week'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('base_salary')
                    ->label(\Modules\Core\Support\FilamentUi::field('base_salary'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('allowance')
                    ->label(\Modules\Core\Support\FilamentUi::field('allowance'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bpjs_tk_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('bpjs_tk_number'))
                    ->searchable(),
                TextColumn::make('bpjs_kes_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('bpjs_kes_number'))
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
                ...ImportTableActions::make(\App\Filament\Imports\TeacherImporter::class),
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
