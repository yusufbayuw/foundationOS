<?php

namespace Modules\Library\Filament\Resources\LibraryAuthors;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Filament\Resources\LibraryAuthors\Pages\CreateLibraryAuthor;
use Modules\Library\Filament\Resources\LibraryAuthors\Pages\EditLibraryAuthor;
use Modules\Library\Filament\Resources\LibraryAuthors\Pages\ListLibraryAuthors;
use Modules\Library\Filament\Resources\LibraryAuthors\Pages\ViewLibraryAuthor;
use Modules\Library\Models\LibraryAuthor;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

class LibraryAuthorResource extends LocalizedResource
{
    protected static ?string $model = LibraryAuthor::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 5;

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
                TextColumn::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Organization'))
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => ListLibraryAuthors::route('/'),
            'create' => CreateLibraryAuthor::route('/create'),
            'view' => ViewLibraryAuthor::route('/{record}'),
            'edit' => EditLibraryAuthor::route('/{record}/edit'),
        ];
    }
}
