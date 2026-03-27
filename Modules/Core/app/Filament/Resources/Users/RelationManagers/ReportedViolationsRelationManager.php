<?php

namespace Modules\Core\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\Violations\ViolationResource;

class ReportedViolationsRelationManager extends RelationManager
{
    protected static string $relationship = 'reportedViolations';

    public function form(Schema $schema): Schema
    {
        return ViolationResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return ViolationResource::table($table);
    }
}
