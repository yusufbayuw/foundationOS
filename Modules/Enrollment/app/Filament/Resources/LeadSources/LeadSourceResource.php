<?php

namespace Modules\Enrollment\Filament\Resources\LeadSources;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Enrollment\Filament\Resources\LeadSources\Pages\CreateLeadSource;
use Modules\Enrollment\Filament\Resources\LeadSources\Pages\EditLeadSource;
use Modules\Enrollment\Filament\Resources\LeadSources\Pages\ListLeadSources;
use Modules\Enrollment\Filament\Resources\LeadSources\Pages\ViewLeadSource;
use Modules\Enrollment\Filament\Resources\LeadSources\Schemas\LeadSourceForm;
use Modules\Enrollment\Filament\Resources\LeadSources\Schemas\LeadSourceInfolist;
use Modules\Enrollment\Filament\Resources\LeadSources\Tables\LeadSourcesTable;
use Modules\Enrollment\Models\LeadSource;

class LeadSourceResource extends ModuleResource
{
    protected static ?string $model = LeadSource::class;

    public static function form(Schema $schema): Schema
    {
        return LeadSourceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeadSourceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadSourcesTable::configure($table);
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
            'index' => ListLeadSources::route('/'),
            'create' => CreateLeadSource::route('/create'),
            'view' => ViewLeadSource::route('/{record}'),
            'edit' => EditLeadSource::route('/{record}/edit'),
        ];
    }
}
