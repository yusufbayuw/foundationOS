<?php

namespace Modules\Employee\Filament\Resources\Employees\Tables;

use App\Filament\Imports\EmployeeImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class EmployeesTable
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
                TextColumn::make('department.name')
                    ->label(FilamentUi::field('department.name'))
                    ->searchable(),
                TextColumn::make('position.name')
                    ->label(FilamentUi::field('position.name'))
                    ->searchable(),
                TextColumn::make('employee_number')
                    ->label(FilamentUi::field('employee_number'))
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
                TextColumn::make('marital_status')
                    ->label(FilamentUi::field('marital_status'))
                    ->searchable(),
                TextColumn::make('id_number')
                    ->label(FilamentUi::field('id_number'))
                    ->searchable(),
                TextColumn::make('npwp')
                    ->label(FilamentUi::field('npwp'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(FilamentUi::field('phone'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(FilamentUi::text('Email address'))
                    ->searchable(),
                TextColumn::make('photo')
                    ->label(FilamentUi::field('photo'))
                    ->searchable(),
                TextColumn::make('employment_type')
                    ->label(FilamentUi::field('employment_type'))
                    ->searchable(),
                TextColumn::make('employment_status')
                    ->label(FilamentUi::field('employment_status'))
                    ->searchable(),
                TextColumn::make('join_date')
                    ->label(FilamentUi::field('join_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(FilamentUi::field('end_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('probation_end_date')
                    ->label(FilamentUi::field('probation_end_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('grade_level')
                    ->label(FilamentUi::field('grade_level'))
                    ->searchable(),
                TextColumn::make('basic_salary')
                    ->label(FilamentUi::field('basic_salary'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bank_account')
                    ->label(FilamentUi::field('bank_account'))
                    ->searchable(),
                TextColumn::make('bank_name')
                    ->label(FilamentUi::field('bank_name'))
                    ->searchable(),
                TextColumn::make('account_holder')
                    ->label(FilamentUi::field('account_holder'))
                    ->searchable(),
                TextColumn::make('bpjs_tk_number')
                    ->label(FilamentUi::field('bpjs_tk_number'))
                    ->searchable(),
                TextColumn::make('bpjs_kes_number')
                    ->label(FilamentUi::field('bpjs_kes_number'))
                    ->searchable(),
                TextColumn::make('insurance_number')
                    ->label(FilamentUi::field('insurance_number'))
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
                SelectFilter::make('employment_status')
                    ->options([
                        'permanent' => 'Permanent',
                        'contract' => 'Contract',
                        'probation' => 'Probation',
                        'part_time' => 'Part Time',
                        'internship' => 'Internship',
                        'resigned' => 'Resigned',
                        'terminated' => 'Terminated',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(EmployeeImporter::class),
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
