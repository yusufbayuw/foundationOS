<?php

namespace Modules\Employee\Filament\Resources\Employees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Core\Filament\Support\ImportTableActions;

class EmployeesTable
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
                TextColumn::make('department.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('department.name'))
                    ->searchable(),
                TextColumn::make('position.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('position.name'))
                    ->searchable(),
                TextColumn::make('employee_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('employee_number'))
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
                TextColumn::make('marital_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('marital_status'))
                    ->searchable(),
                TextColumn::make('id_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('id_number'))
                    ->searchable(),
                TextColumn::make('npwp')
                    ->label(\Modules\Core\Support\FilamentUi::field('npwp'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                    ->searchable(),
                TextColumn::make('photo')
                    ->label(\Modules\Core\Support\FilamentUi::field('photo'))
                    ->searchable(),
                TextColumn::make('employment_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('employment_type'))
                    ->searchable(),
                TextColumn::make('employment_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('employment_status'))
                    ->searchable(),
                TextColumn::make('join_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('join_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('probation_end_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('probation_end_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('grade_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_level'))
                    ->searchable(),
                TextColumn::make('basic_salary')
                    ->label(\Modules\Core\Support\FilamentUi::field('basic_salary'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bank_account')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_account'))
                    ->searchable(),
                TextColumn::make('bank_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_name'))
                    ->searchable(),
                TextColumn::make('account_holder')
                    ->label(\Modules\Core\Support\FilamentUi::field('account_holder'))
                    ->searchable(),
                TextColumn::make('bpjs_tk_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('bpjs_tk_number'))
                    ->searchable(),
                TextColumn::make('bpjs_kes_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('bpjs_kes_number'))
                    ->searchable(),
                TextColumn::make('insurance_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('insurance_number'))
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
                ...ImportTableActions::make(\App\Filament\Imports\EmployeeImporter::class),
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
