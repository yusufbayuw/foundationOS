<?php

namespace Modules\School\Filament\Resources\Students\Tables;

use App\Filament\Imports\StudentImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;
use Modules\School\Filament\Exports\StudentExporter;

class StudentsTable
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
                TextColumn::make('academicYear.name')
                    ->label(FilamentUi::field('academicYear.name'))
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(FilamentUi::field('user.name'))
                    ->searchable(),
                TextColumn::make('nis')
                    ->label(FilamentUi::field('nis'))
                    ->searchable(),
                TextColumn::make('nisn')
                    ->label(FilamentUi::field('nisn'))
                    ->searchable(),
                TextColumn::make('entry_date')
                    ->label(FilamentUi::field('entry_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('entry_type')
                    ->label(FilamentUi::field('entry_type'))
                    ->searchable(),
                TextColumn::make('previous_school')
                    ->label(FilamentUi::field('previous_school'))
                    ->searchable(),
                TextColumn::make('previous_school_npsn')
                    ->label(FilamentUi::field('previous_school_npsn'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('graduation_date')
                    ->label(FilamentUi::field('graduation_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('ijazah_number')
                    ->label(FilamentUi::field('ijazah_number'))
                    ->searchable(),
                TextColumn::make('skhun_number')
                    ->label(FilamentUi::field('skhun_number'))
                    ->searchable(),
                TextColumn::make('track')
                    ->label(FilamentUi::field('track'))
                    ->searchable(),
                TextColumn::make('scholarship_status')
                    ->label(FilamentUi::field('scholarship_status'))
                    ->searchable(),
                TextColumn::make('family_card_number')
                    ->label(FilamentUi::field('family_card_number'))
                    ->searchable(),
                TextColumn::make('father_name')
                    ->label(FilamentUi::field('father_name'))
                    ->searchable(),
                TextColumn::make('father_nik')
                    ->label(FilamentUi::field('father_nik'))
                    ->searchable(),
                TextColumn::make('father_education')
                    ->label(FilamentUi::field('father_education'))
                    ->searchable(),
                TextColumn::make('father_job')
                    ->label(FilamentUi::field('father_job'))
                    ->searchable(),
                TextColumn::make('father_phone')
                    ->label(FilamentUi::field('father_phone'))
                    ->searchable(),
                TextColumn::make('mother_name')
                    ->label(FilamentUi::field('mother_name'))
                    ->searchable(),
                TextColumn::make('mother_nik')
                    ->label(FilamentUi::field('mother_nik'))
                    ->searchable(),
                TextColumn::make('mother_education')
                    ->label(FilamentUi::field('mother_education'))
                    ->searchable(),
                TextColumn::make('mother_job')
                    ->label(FilamentUi::field('mother_job'))
                    ->searchable(),
                TextColumn::make('mother_phone')
                    ->label(FilamentUi::field('mother_phone'))
                    ->searchable(),
                TextColumn::make('guardian_name')
                    ->label(FilamentUi::field('guardian_name'))
                    ->searchable(),
                TextColumn::make('guardian_relation')
                    ->label(FilamentUi::field('guardian_relation'))
                    ->searchable(),
                TextColumn::make('guardian_phone')
                    ->label(FilamentUi::field('guardian_phone'))
                    ->searchable(),
                TextColumn::make('residence_type')
                    ->label(FilamentUi::field('residence_type'))
                    ->searchable(),
                TextColumn::make('transport_type')
                    ->label(FilamentUi::field('transport_type'))
                    ->searchable(),
                TextColumn::make('travel_time_minutes')
                    ->label(FilamentUi::field('travel_time_minutes'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('distance_km')
                    ->label(FilamentUi::field('distance_km'))
                    ->numeric()
                    ->sortable(),
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
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'graduated' => 'Graduated',
                        'transferred' => 'Transferred',
                        'dropped_out' => 'Dropped Out',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(StudentExporter::class),
                ...ImportTableActions::make(StudentImporter::class),
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
