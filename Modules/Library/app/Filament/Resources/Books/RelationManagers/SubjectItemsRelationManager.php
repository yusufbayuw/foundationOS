<?php

namespace Modules\Library\Filament\Resources\Books\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\LibrarySubjects\LibrarySubjectResource;

class SubjectItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'subjectItems';

    public function form(Schema $schema): Schema
    {
        return LibrarySubjectResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return LibrarySubjectResource::table($table);
    }
}
