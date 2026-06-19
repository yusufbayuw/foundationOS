<?php

namespace Modules\Library\Filament\Resources\LibrarySerialIssues;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;
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
                ->label(FilamentUi::field('serial_id'))
                ->relationship('serial', 'title')
                ->searchable()
                ->preload()
                ->required(),
            DatePicker::make('expected_date')
                ->label(FilamentUi::field('expected_date')),
            DatePicker::make('received_date')
                ->label(FilamentUi::field('received_date')),
            TextInput::make('sequence_number')
                ->label(FilamentUi::field('sequence_number')),
            TextInput::make('status')
                ->label(FilamentUi::field('status'))
                ->default('expected'),
            Textarea::make('notes')
                ->label(FilamentUi::field('notes'))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serial.title')
                    ->label(FilamentUi::text('Serial'))
                    ->searchable(),
                TextColumn::make('sequence_number')
                    ->label(FilamentUi::field('sequence_number'))
                    ->searchable(),
                TextColumn::make('expected_date')
                    ->label(FilamentUi::field('expected_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('received_date')
                    ->label(FilamentUi::field('received_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
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
