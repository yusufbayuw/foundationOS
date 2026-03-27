<?php

namespace Modules\Library\Filament\Resources\LibrarySerialIssues;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Filament\Resources\LibrarySerialIssues\Pages\CreateLibrarySerialIssue;
use Modules\Library\Filament\Resources\LibrarySerialIssues\Pages\EditLibrarySerialIssue;
use Modules\Library\Filament\Resources\LibrarySerialIssues\Pages\ListLibrarySerialIssues;
use Modules\Library\Filament\Resources\LibrarySerialIssues\Pages\ViewLibrarySerialIssue;
use Modules\Library\Models\LibrarySerialIssue;

class LibrarySerialIssueResource extends LocalizedResource
{
    protected static ?string $model = LibrarySerialIssue::class;

    protected static ?string $recordTitleAttribute = 'sequence_number';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('serial_id')
                ->label(\Modules\Core\Support\FilamentUi::field('serial_id'))
                ->relationship('serial', 'title')
                ->searchable()
                ->preload()
                ->required(),
            DatePicker::make('expected_date')
                ->label(\Modules\Core\Support\FilamentUi::field('expected_date')),
            DatePicker::make('received_date')
                ->label(\Modules\Core\Support\FilamentUi::field('received_date')),
            TextInput::make('sequence_number')
                ->label(\Modules\Core\Support\FilamentUi::field('sequence_number')),
            TextInput::make('status')
                ->label(\Modules\Core\Support\FilamentUi::field('status'))
                ->default('expected'),
            Textarea::make('notes')
                ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serial.title')
                    ->label(\Modules\Core\Support\FilamentUi::text('Serial'))
                    ->searchable(),
                TextColumn::make('sequence_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('sequence_number'))
                    ->searchable(),
                TextColumn::make('expected_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('expected_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('received_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('received_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLibrarySerialIssues::route('/'),
            'create' => CreateLibrarySerialIssue::route('/create'),
            'view' => ViewLibrarySerialIssue::route('/{record}'),
            'edit' => EditLibrarySerialIssue::route('/{record}/edit'),
        ];
    }
}
