<?php

namespace Modules\Library\Filament\Resources\LibrarySerials\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\LibrarySerialIssues\LibrarySerialIssueResource;

class IssuesRelationManager extends RelationManager
{
    protected static string $relationship = 'issues';

    public function form(Schema $schema): Schema
    {
        return LibrarySerialIssueResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return LibrarySerialIssueResource::table($table);
    }
}
