<?php

namespace Modules\Library\Filament\Resources\LibraryItemStatuses;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Filament\Resources\LibraryItemStatuses\Pages\CreateLibraryItemStatus;
use Modules\Library\Filament\Resources\LibraryItemStatuses\Pages\EditLibraryItemStatus;
use Modules\Library\Filament\Resources\LibraryItemStatuses\Pages\ListLibraryItemStatuses;
use Modules\Library\Filament\Resources\LibraryItemStatuses\Pages\ViewLibraryItemStatus;
use Modules\Library\Models\LibraryItemStatus;

class LibraryItemStatusResource extends LocalizedResource
{
    protected static ?string $model = LibraryItemStatus::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('tenant_id')
                ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                ->relationship('tenant', 'name')
                ->default(Filament::getTenant()?->getKey())
                ->disabled(Filament::getTenant() !== null)
                ->dehydrated()
                ->required(),
            Select::make('organization_id')
                ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                    if (Filament::getTenant()) {
                        $query->where('tenant_id', Filament::getTenant()->getKey());
                    }
                })
                ->nullable()
                ->helperText('Opsional. Kosongkan untuk data tenant-wide.'),
            TextInput::make('code')
                ->label(\Modules\Core\Support\FilamentUi::field('code')),
            TextInput::make('name')
                ->label(\Modules\Core\Support\FilamentUi::field('name'))
                ->required(),
            Toggle::make('is_loanable')
                ->label(\Modules\Core\Support\FilamentUi::text('Loanable'))
                ->default(true),
            Toggle::make('is_active')
                ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                ->default(true),
            Textarea::make('notes')
                ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                IconColumn::make('is_loanable')
                    ->label(\Modules\Core\Support\FilamentUi::text('Loanable'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
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
            'index' => ListLibraryItemStatuses::route('/'),
            'create' => CreateLibraryItemStatus::route('/create'),
            'view' => ViewLibraryItemStatus::route('/{record}'),
            'edit' => EditLibraryItemStatus::route('/{record}/edit'),
        ];
    }
}
