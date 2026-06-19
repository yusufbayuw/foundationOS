<?php

namespace Modules\Employee\Filament\Resources\EmploymentContracts\Tables;

use App\Filament\Imports\EmploymentContractImporter;
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

class EmploymentContractsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('employee.id')
                    ->label(FilamentUi::field('employee.id'))
                    ->searchable(),
                TextColumn::make('previousContract.id')
                    ->label(FilamentUi::field('previousContract.id'))
                    ->searchable(),
                TextColumn::make('contract_number')
                    ->label(FilamentUi::field('contract_number'))
                    ->searchable(),
                TextColumn::make('contract_type')
                    ->label(FilamentUi::field('contract_type'))
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label(FilamentUi::field('start_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(FilamentUi::field('end_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('probation_period_months')
                    ->label(FilamentUi::field('probation_period_months'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('basic_salary')
                    ->label(FilamentUi::field('basic_salary'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('work_location')
                    ->label(FilamentUi::field('work_location'))
                    ->searchable(),
                TextColumn::make('work_hours_per_week')
                    ->label(FilamentUi::field('work_hours_per_week'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                IconColumn::make('signed_by_employee')
                    ->label(FilamentUi::field('signed_by_employee'))
                    ->boolean(),
                IconColumn::make('signed_by_employer')
                    ->label(FilamentUi::field('signed_by_employer'))
                    ->boolean(),
                TextColumn::make('document_file')
                    ->label(FilamentUi::field('document_file'))
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
                ...ImportTableActions::make(EmploymentContractImporter::class),
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
