<?php

namespace Modules\Library\Filament\Resources\LibraryGmds;

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
use Modules\Library\Filament\Resources\LibraryGmds\Pages\CreateLibraryGmd;
use Modules\Library\Filament\Resources\LibraryGmds\Pages\EditLibraryGmd;
use Modules\Library\Filament\Resources\LibraryGmds\Pages\ListLibraryGmds;
use Modules\Library\Filament\Resources\LibraryGmds\Pages\ViewLibraryGmd;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Models\LibraryGmd;

class LibraryGmdResource extends LocalizedResource
{
    protected static ?string $model = LibraryGmd::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 11;

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
            'index' => ListLibraryGmds::route('/'),
            'create' => CreateLibraryGmd::route('/create'),
            'view' => ViewLibraryGmd::route('/{record}'),
            'edit' => EditLibraryGmd::route('/{record}/edit'),
        ];
    }
}
