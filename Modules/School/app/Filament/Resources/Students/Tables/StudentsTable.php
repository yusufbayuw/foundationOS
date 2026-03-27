<?php

namespace Modules\School\Filament\Resources\Students\Tables;

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
use Filament\Actions\ExportAction;
use Modules\School\Filament\Exports\StudentExporter;

class StudentsTable
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
                TextColumn::make('academicYear.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('academicYear.name'))
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('user.name'))
                    ->searchable(),
                TextColumn::make('nis')
                    ->label(\Modules\Core\Support\FilamentUi::field('nis'))
                    ->searchable(),
                TextColumn::make('nisn')
                    ->label(\Modules\Core\Support\FilamentUi::field('nisn'))
                    ->searchable(),
                TextColumn::make('entry_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('entry_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_type'))
                    ->searchable(),
                TextColumn::make('previous_school')
                    ->label(\Modules\Core\Support\FilamentUi::field('previous_school'))
                    ->searchable(),
                TextColumn::make('previous_school_npsn')
                    ->label(\Modules\Core\Support\FilamentUi::field('previous_school_npsn'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('graduation_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('graduation_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('ijazah_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('ijazah_number'))
                    ->searchable(),
                TextColumn::make('skhun_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('skhun_number'))
                    ->searchable(),
                TextColumn::make('track')
                    ->label(\Modules\Core\Support\FilamentUi::field('track'))
                    ->searchable(),
                TextColumn::make('scholarship_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('scholarship_status'))
                    ->searchable(),
                TextColumn::make('family_card_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('family_card_number'))
                    ->searchable(),
                TextColumn::make('father_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('father_name'))
                    ->searchable(),
                TextColumn::make('father_nik')
                    ->label(\Modules\Core\Support\FilamentUi::field('father_nik'))
                    ->searchable(),
                TextColumn::make('father_education')
                    ->label(\Modules\Core\Support\FilamentUi::field('father_education'))
                    ->searchable(),
                TextColumn::make('father_job')
                    ->label(\Modules\Core\Support\FilamentUi::field('father_job'))
                    ->searchable(),
                TextColumn::make('father_phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('father_phone'))
                    ->searchable(),
                TextColumn::make('mother_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('mother_name'))
                    ->searchable(),
                TextColumn::make('mother_nik')
                    ->label(\Modules\Core\Support\FilamentUi::field('mother_nik'))
                    ->searchable(),
                TextColumn::make('mother_education')
                    ->label(\Modules\Core\Support\FilamentUi::field('mother_education'))
                    ->searchable(),
                TextColumn::make('mother_job')
                    ->label(\Modules\Core\Support\FilamentUi::field('mother_job'))
                    ->searchable(),
                TextColumn::make('mother_phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('mother_phone'))
                    ->searchable(),
                TextColumn::make('guardian_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('guardian_name'))
                    ->searchable(),
                TextColumn::make('guardian_relation')
                    ->label(\Modules\Core\Support\FilamentUi::field('guardian_relation'))
                    ->searchable(),
                TextColumn::make('guardian_phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('guardian_phone'))
                    ->searchable(),
                TextColumn::make('residence_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('residence_type'))
                    ->searchable(),
                TextColumn::make('transport_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('transport_type'))
                    ->searchable(),
                TextColumn::make('travel_time_minutes')
                    ->label(\Modules\Core\Support\FilamentUi::field('travel_time_minutes'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('distance_km')
                    ->label(\Modules\Core\Support\FilamentUi::field('distance_km'))
                    ->numeric()
                    ->sortable(),
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
                ExportAction::make()
                    ->exporter(StudentExporter::class),
                ...ImportTableActions::make(\App\Filament\Imports\StudentImporter::class),
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
