<?php

namespace Modules\Finance\Filament\Resources\JournalEntryLines;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Finance\Filament\Resources\JournalEntryLines\Pages\CreateJournalEntryLine;
use Modules\Finance\Filament\Resources\JournalEntryLines\Pages\EditJournalEntryLine;
use Modules\Finance\Filament\Resources\JournalEntryLines\Pages\ListJournalEntryLines;
use Modules\Finance\Filament\Resources\JournalEntryLines\Pages\ViewJournalEntryLine;
use Modules\Finance\Filament\Resources\JournalEntryLines\Schemas\JournalEntryLineForm;
use Modules\Finance\Filament\Resources\JournalEntryLines\Schemas\JournalEntryLineInfolist;
use Modules\Finance\Filament\Resources\JournalEntryLines\Tables\JournalEntryLinesTable;
use Modules\Finance\Models\JournalEntryLine;

class JournalEntryLineResource extends LocalizedResource
{
    protected static ?string $model = JournalEntryLine::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return JournalEntryLineForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return JournalEntryLineInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JournalEntryLinesTable::configure($table);
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
            'index' => ListJournalEntryLines::route('/'),
            'create' => CreateJournalEntryLine::route('/create'),
            'view' => ViewJournalEntryLine::route('/{record}'),
            'edit' => EditJournalEntryLine::route('/{record}/edit'),
        ];
    }
}
