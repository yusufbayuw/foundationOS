<?php

namespace Modules\Donation\Filament\Resources\Donors\Tables;

use App\Filament\Imports\DonorImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;
use Modules\Donation\Filament\Exports\DonorExporter;
use Modules\Donation\Models\Donor;

class DonorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(FilamentUi::field('email'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label(FilamentUi::field('phone'))
                    ->toggleable(),
                IconColumn::make('is_anonymous')
                    ->label(FilamentUi::field('is_anonymous'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])

            ->headerActions([
                ExportAction::make()
                    ->exporter(DonorExporter::class)
                    ->visible(fn (): bool => auth()->user()?->can('export', Donor::class) ?? false)
                    ->authorize(fn (): bool => auth()->user()?->can('export', Donor::class) ?? false),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                ...ImportTableActions::make(DonorImporter::class),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
