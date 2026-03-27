<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\CourseOfferings\CourseOfferingResource;

class CourseOfferingsRelationManager extends RelationManager
{
    protected static string $relationship = 'courseOfferings';

    public function form(Schema $schema): Schema
    {
        return CourseOfferingResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return CourseOfferingResource::table($table);
    }
}
