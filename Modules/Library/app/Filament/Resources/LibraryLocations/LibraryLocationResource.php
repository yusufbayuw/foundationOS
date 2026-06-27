<?php

namespace Modules\Library\Filament\Resources\LibraryLocations;

use Filament\Facades\Filament;
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
use Modules\Library\Filament\Resources\LibraryLocations\Pages\CreateLibraryLocation;
use Modules\Library\Filament\Resources\LibraryLocations\Pages\EditLibraryLocation;
use Modules\Library\Filament\Resources\LibraryLocations\Pages\ListLibraryLocations;
use Modules\Library\Filament\Resources\LibraryLocations\Pages\ViewLibraryLocation;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Models\LibraryLocation;

class LibraryLocationResource extends LocalizedResource
{
    protected static ?string $model = LibraryLocation::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 8;

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
                ->helperText(FilamentUi::text('Optional. Leave blank for tenant-wide data.')),
            TextInput::make('code')
                ->label(FilamentUi::field('code')),
            TextInput::make('name')
                ->label(FilamentUi::field('name'))
                ->required(),
            TextInput::make('room')
                ->label(FilamentUi::field('room')),
            TextInput::make('shelf')
                ->label(FilamentUi::field('shelf')),
            Toggle::make('is_active')
                ->label(FilamentUi::field('is_active'))
                ->default(true),
            Textarea::make('notes')
                ->label(FilamentUi::field('notes'))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('room')
                    ->label(FilamentUi::field('room'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('shelf')
                    ->label(FilamentUi::field('shelf'))
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label(FilamentUi::field('is_active'))
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

    public static function getPages(): array
    {
        return [
            'index' => ListLibraryLocations::route('/'),
            'create' => CreateLibraryLocation::route('/create'),
            'view' => ViewLibraryLocation::route('/{record}'),
            'edit' => EditLibraryLocation::route('/{record}/edit'),
        ];
    }
}
