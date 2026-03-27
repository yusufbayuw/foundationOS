<?php

namespace Modules\Library\Filament\Resources\LibraryPolicies;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\LibraryPolicies\Pages\CreateLibraryPolicy;
use Modules\Library\Filament\Resources\LibraryPolicies\Pages\EditLibraryPolicy;
use Modules\Library\Filament\Resources\LibraryPolicies\Pages\ListLibraryPolicies;
use Modules\Library\Filament\Resources\LibraryPolicies\Pages\ViewLibraryPolicy;
use Modules\Library\Filament\Resources\LibraryPolicies\Schemas\LibraryPolicyForm;
use Modules\Library\Filament\Resources\LibraryPolicies\Schemas\LibraryPolicyInfolist;
use Modules\Library\Filament\Resources\LibraryPolicies\Tables\LibraryPoliciesTable;
use Modules\Library\Filament\Resources\LibraryResource;
use Modules\Library\Models\LibraryPolicy;

class LibraryPolicyResource extends LibraryResource
{
    protected static ?string $model = LibraryPolicy::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LibraryPolicyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LibraryPolicyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LibraryPoliciesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLibraryPolicies::route('/'),
            'create' => CreateLibraryPolicy::route('/create'),
            'view' => ViewLibraryPolicy::route('/{record}'),
            'edit' => EditLibraryPolicy::route('/{record}/edit'),
        ];
    }
}
