<?php

namespace Modules\Ai\Filament\Resources\AiPromptTemplates\Tables;

use App\Filament\Imports\AiPromptTemplateImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class AiPromptTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('organization.name')
                    ->label(FilamentUi::field('organization_id'))
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(FilamentUi::field('status'))
                    ->options([
                        'active' => FilamentUi::text('Active'),
                        'inactive' => FilamentUi::text('Inactive'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                ...ImportTableActions::make(AiPromptTemplateImporter::class),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
