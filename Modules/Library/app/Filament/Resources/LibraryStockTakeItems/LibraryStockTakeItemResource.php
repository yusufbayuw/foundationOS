<?php

namespace Modules\Library\Filament\Resources\LibraryStockTakeItems;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Filament\Resources\LibraryStockTakeItems\Pages\CreateLibraryStockTakeItem;
use Modules\Library\Filament\Resources\LibraryStockTakeItems\Pages\EditLibraryStockTakeItem;
use Modules\Library\Filament\Resources\LibraryStockTakeItems\Pages\ListLibraryStockTakeItems;
use Modules\Library\Filament\Resources\LibraryStockTakeItems\Pages\ViewLibraryStockTakeItem;
use Modules\Library\Models\LibraryStockTakeItem;

class LibraryStockTakeItemResource extends LocalizedResource
{
    protected static ?string $model = LibraryStockTakeItem::class;

    protected static ?string $recordTitleAttribute = 'status';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('stock_take_id')
                ->label(\Modules\Core\Support\FilamentUi::field('stock_take_id'))
                ->relationship('stockTake', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('book_copy_id')
                ->label(\Modules\Core\Support\FilamentUi::field('book_copy_id'))
                ->relationship('bookCopy', 'copy_number')
                ->searchable()
                ->preload()
                ->nullable(),
            Select::make('scanned_by')
                ->label(\Modules\Core\Support\FilamentUi::field('scanned_by'))
                ->relationship('scannedBy', 'name')
                ->searchable()
                ->preload()
                ->nullable(),
            DateTimePicker::make('scanned_at')
                ->label(\Modules\Core\Support\FilamentUi::field('scanned_at')),
            TextInput::make('status')
                ->label(\Modules\Core\Support\FilamentUi::field('status'))
                ->default('found'),
            Textarea::make('notes')
                ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('stockTake.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Stock Take'))
                    ->searchable(),
                TextColumn::make('bookCopy.copy_number')
                    ->label(\Modules\Core\Support\FilamentUi::text('Copy'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('scanned_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('scanned_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLibraryStockTakeItems::route('/'),
            'create' => CreateLibraryStockTakeItem::route('/create'),
            'view' => ViewLibraryStockTakeItem::route('/{record}'),
            'edit' => EditLibraryStockTakeItem::route('/{record}/edit'),
        ];
    }
}
