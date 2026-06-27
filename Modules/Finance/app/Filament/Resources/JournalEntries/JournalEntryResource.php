<?php

namespace Modules\Finance\Filament\Resources\JournalEntries;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Filament\Concerns\ConfiguresGlobalSearch;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Filament\Resources\JournalEntries\Pages\CreateJournalEntry;
use Modules\Finance\Filament\Resources\JournalEntries\Pages\EditJournalEntry;
use Modules\Finance\Filament\Resources\JournalEntries\Pages\ListJournalEntries;
use Modules\Finance\Filament\Resources\JournalEntries\Pages\ViewJournalEntry;
use Modules\Finance\Filament\Resources\JournalEntries\RelationManagers\LinesRelationManager;
use Modules\Finance\Filament\Resources\JournalEntries\Schemas\JournalEntryForm;
use Modules\Finance\Filament\Resources\JournalEntries\Schemas\JournalEntryInfolist;
use Modules\Finance\Filament\Resources\JournalEntries\Tables\JournalEntriesTable;
use Modules\Finance\Models\JournalEntry;

class JournalEntryResource extends LocalizedResource
{
    use ConfiguresGlobalSearch;

    protected static ?string $model = JournalEntry::class;

    protected static ?string $recordTitleAttribute = 'entry_number';

    /**
     * @return array<int, string>
     */
    protected static function globalSearchAttributes(): array
    {
        return ['entry_number', 'description'];
    }

    /**
     * @return array<string, string>
     */
    protected static function globalSearchResultDetails(Model $record): array
    {
        assert($record instanceof JournalEntry);

        return [
            FilamentUi::field('is_posted') => $record->is_posted ? 'yes' : 'no',
        ];
    }

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

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
            LinesRelationManager::class,
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

    public static function canEdit(Model $record): bool
    {
        return $record instanceof JournalEntry
            && ! $record->isLockedForMutation()
            && parent::canEdit($record);
    }
}
