<?php

namespace Modules\Legal\Filament\Resources\LegalDocuments\Tables;

use App\Filament\Imports\LegalDocumentImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class LegalDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label(FilamentUi::field('code'))->searchable(),
                TextColumn::make('name')->label(FilamentUi::field('name'))->searchable()->sortable(),
                TextColumn::make('status')->label(FilamentUi::field('status'))->badge(),
            ])
            ->defaultSort('name')
            ->toolbarActions([
                ...ImportTableActions::make(LegalDocumentImporter::class),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
