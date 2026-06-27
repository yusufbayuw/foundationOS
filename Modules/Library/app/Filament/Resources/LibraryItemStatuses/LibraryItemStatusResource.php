<?php

namespace Modules\Library\Filament\Resources\LibraryItemStatuses;

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
use Modules\Library\Filament\Resources\LibraryItemStatuses\Pages\CreateLibraryItemStatus;
use Modules\Library\Filament\Resources\LibraryItemStatuses\Pages\EditLibraryItemStatus;
use Modules\Library\Filament\Resources\LibraryItemStatuses\Pages\ListLibraryItemStatuses;
use Modules\Library\Filament\Resources\LibraryItemStatuses\Pages\ViewLibraryItemStatus;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Models\LibraryItemStatus;

class LibraryItemStatusResource extends LocalizedResource
{
    protected static ?string $model = LibraryItemStatus::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TenantField::make(),
            Select::make('organization_id')
                ->label(FilamentUi::field('organization_id'))
                ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                    if (current_tenant_model()) {
                        $query->where('tenant_id', current_tenant_id());
                    }
                })
                ->nullable()
                ->helperText(FilamentUi::text('Optional. Leave blank for tenant-wide data.')),
            TextInput::make('code')
                ->label(FilamentUi::field('code')),
            TextInput::make('name')
                ->label(FilamentUi::field('name'))
                ->required(),
            Toggle::make('is_loanable')
                ->label(FilamentUi::text('Loanable'))
                ->default(true),
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
                IconColumn::make('is_loanable')
                    ->label(FilamentUi::text('Loanable'))
                    ->boolean(),
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
            'index' => ListLibraryItemStatuses::route('/'),
            'create' => CreateLibraryItemStatus::route('/create'),
            'view' => ViewLibraryItemStatus::route('/{record}'),
            'edit' => EditLibraryItemStatus::route('/{record}/edit'),
        ];
    }
}
