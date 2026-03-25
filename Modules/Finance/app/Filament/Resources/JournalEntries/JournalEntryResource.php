<?php

namespace Modules\Finance\Filament\Resources\JournalEntries;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\JournalEntries\Pages\CreateJournalEntry;
use Modules\Finance\Filament\Resources\JournalEntries\Pages\EditJournalEntry;
use Modules\Finance\Filament\Resources\JournalEntries\Pages\ListJournalEntries;
use Modules\Finance\Filament\Resources\JournalEntries\Pages\ViewJournalEntry;
use Modules\Finance\Filament\Resources\JournalEntries\Schemas\JournalEntryForm;
use Modules\Finance\Filament\Resources\JournalEntries\Schemas\JournalEntryInfolist;
use Modules\Finance\Filament\Resources\JournalEntries\Tables\JournalEntriesTable;
use Modules\Finance\Models\JournalEntry;

class JournalEntryResource extends LocalizedResource
{
    protected static ?string $model = JournalEntry::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return JournalEntryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return JournalEntryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JournalEntriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJournalEntries::route('/'),
            'create' => CreateJournalEntry::route('/create'),
            'view' => ViewJournalEntry::route('/{record}'),
            'edit' => EditJournalEntry::route('/{record}/edit'),
        ];
    }
}
