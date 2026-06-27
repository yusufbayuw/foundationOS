<?php

namespace Modules\Library\Filament\Resources\LibraryStockTakes;

use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Filament\Resources\LibraryStockTakes\Pages\CreateLibraryStockTake;
use Modules\Library\Filament\Resources\LibraryStockTakes\Pages\EditLibraryStockTake;
use Modules\Library\Filament\Resources\LibraryStockTakes\Pages\ListLibraryStockTakes;
use Modules\Library\Filament\Resources\LibraryStockTakes\Pages\ViewLibraryStockTake;
use Modules\Library\Filament\Resources\LibraryStockTakes\RelationManagers\ItemsRelationManager;
use Modules\Library\Models\LibraryStockTake;

class LibraryStockTakeResource extends LocalizedResource
{
    protected static ?string $model = LibraryStockTake::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 95;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TenantField::make(),
            Select::make('organization_id')
                ->label(FilamentUi::field('organization_id'))
                ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                    if (Filament::getTenant()) {
                        $query->where($query->getModel()->qualifyColumn('tenant_id'), Filament::getTenant()->getKey());
                    }
                })
                ->nullable()
                ->helperText(FilamentUi::text('Optional. Leave blank for tenant-wide stock takes.')),
            Select::make('processed_by')
                ->label(FilamentUi::field('processed_by'))
                ->relationship('processedBy', 'name')
                ->searchable()
                ->preload()
                ->nullable(),
            TextInput::make('name')
                ->label(FilamentUi::field('name'))
                ->required(),
            DateTimePicker::make('started_at')
                ->label(FilamentUi::field('started_at')),
            DateTimePicker::make('ended_at')
                ->label(FilamentUi::field('ended_at')),
            TextInput::make('status')
                ->label(FilamentUi::field('status'))
                ->default('draft'),
            Toggle::make('status_completed')
                ->label(FilamentUi::text('Completed'))
                ->default(false)
                ->dehydrated(false)
                ->afterStateHydrated(function (Toggle $component, ?LibraryStockTake $record): void {
                    $component->state($record?->status === 'completed');
                })
                ->afterStateUpdated(function (Toggle $component, $state, callable $set): void {
                    $set('status', $state ? 'completed' : 'draft');
                }),
            Textarea::make('notes')
                ->label(FilamentUi::field('notes'))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('started_at')
                    ->label(FilamentUi::field('started_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ended_at')
                    ->label(FilamentUi::field('ended_at'))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('processed_by')
                    ->label(FilamentUi::field('processed_by'))
                    ->state(fn (LibraryStockTake $record) => (bool) $record->processed_by)
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label(FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLibraryStockTakes::route('/'),
            'create' => CreateLibraryStockTake::route('/create'),
            'view' => ViewLibraryStockTake::route('/{record}'),
            'edit' => EditLibraryStockTake::route('/{record}/edit'),
        ];
    }
}
