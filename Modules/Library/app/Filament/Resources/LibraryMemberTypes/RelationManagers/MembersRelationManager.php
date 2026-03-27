<?php

namespace Modules\Library\Filament\Resources\LibraryMemberTypes\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\Members\Schemas\MemberForm;
use Modules\Library\Filament\Resources\Members\Tables\MembersTable;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    public function form(Schema $schema): Schema
    {
        return MemberForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return MembersTable::configure($table);
    }
}
