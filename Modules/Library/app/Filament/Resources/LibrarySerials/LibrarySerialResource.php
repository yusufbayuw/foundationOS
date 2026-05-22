<?php

namespace Modules\Library\Filament\Resources\LibrarySerials;

use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
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
use Modules\Library\Filament\Resources\LibrarySerials\Pages\CreateLibrarySerial;
use Modules\Library\Filament\Resources\LibrarySerials\Pages\EditLibrarySerial;
use Modules\Library\Filament\Resources\LibrarySerials\Pages\ListLibrarySerials;
use Modules\Library\Filament\Resources\LibrarySerials\Pages\ViewLibrarySerial;
use Modules\Library\Filament\Resources\LibrarySerials\RelationManagers\IssuesRelationManager;
use Modules\Library\Models\LibrarySerial;
use Modules\Library\Support\LibraryScopeResolver;

class LibrarySerialResource extends LocalizedResource
{
    protected static ?string $model = LibrarySerial::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 90;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TenantField::make(),
            Select::make('organization_id')
                ->label(FilamentUi::field('organization_id'))
                ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                    if (Filament::getTenant()) {
                        $query->where('tenant_id', Filament::getTenant()->getKey());
                    }
                })
                ->nullable()
                ->helperText(FilamentUi::text('Optional. Leave blank for tenant-wide data.')),
            Select::make('book_id')
                ->label(FilamentUi::field('book_id'))
                ->relationship('book', 'title', modifyQueryUsing: function (Builder $query): void {
                    app(LibraryScopeResolver::class)->apply($query, auth()->user());
                })
                ->searchable()
                ->preload()
                ->nullable(),
            Select::make('frequency_id')
                ->label(FilamentUi::field('frequency_id'))
                ->relationship('frequency', 'name', modifyQueryUsing: function (Builder $query): void {
                    app(LibraryScopeResolver::class)->apply($query, auth()->user());
                })
                ->searchable()
                ->preload()
                ->nullable(),
            TextInput::make('title')
                ->label(FilamentUi::field('title'))
                ->required(),
            TextInput::make('issn')
                ->label(FilamentUi::field('issn')),
            DatePicker::make('start_date')
                ->label(FilamentUi::field('start_date')),
            TextInput::make('period')
                ->label(FilamentUi::field('period')),
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
                TextColumn::make('title')
                    ->label(FilamentUi::field('title'))
                    ->searchable(),
                TextColumn::make('issn')
                    ->label(FilamentUi::field('issn'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('frequency.name')
                    ->label(FilamentUi::text('Frequency'))
                    ->placeholder('-'),
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

    public static function getRelations(): array
    {
        return [
            IssuesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLibrarySerials::route('/'),
            'create' => CreateLibrarySerial::route('/create'),
            'view' => ViewLibrarySerial::route('/{record}'),
            'edit' => EditLibrarySerial::route('/{record}/edit'),
        ];
    }
}
