<?php

namespace Modules\Workflow\Filament\Resources\Workflows\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class WorkflowsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(FilamentUi::field('name'))->searchable(),
                TextColumn::make('code')->label(FilamentUi::field('code'))->searchable(),
                TextColumn::make('module')->label(FilamentUi::field('module'))->searchable()->placeholder('-'),
                TextColumn::make('organization.name')->label(FilamentUi::field('organization_id'))->placeholder('-'),
                TextColumn::make('version')->label(FilamentUi::field('version'))->sortable(),
                TextColumn::make('status')->label(FilamentUi::field('status'))->badge(),
                IconColumn::make('is_active')->label(FilamentUi::field('is_active'))->boolean(),
                TextColumn::make('published_at')->label(FilamentUi::field('published_at'))->dateTime()->sortable()->placeholder('-'),
                TextColumn::make('updated_at')->label(FilamentUi::field('updated_at'))->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ]);
    }
}
