<?php

namespace Modules\Core\Filament\Resources\Organizations;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\Organizations\Pages\CreateOrganization;
use Modules\Core\Filament\Resources\Organizations\Pages\EditOrganization;
use Modules\Core\Filament\Resources\Organizations\Pages\ListOrganizations;
use Modules\Core\Filament\Resources\Organizations\Pages\ViewOrganization;
use Modules\Core\Filament\Resources\Organizations\Schemas\OrganizationForm;
use Modules\Core\Filament\Resources\Organizations\Schemas\OrganizationInfolist;
use Modules\Core\Filament\Resources\Organizations\Tables\OrganizationsTable;
use Modules\Core\Models\Organization;

class OrganizationResource extends LocalizedResource
{
    protected static ?string $model = Organization::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return OrganizationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrganizationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrganizationsTable::configure($table);
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
            'index' => ListOrganizations::route('/'),
            'create' => CreateOrganization::route('/create'),
            'view' => ViewOrganization::route('/{record}'),
            'edit' => EditOrganization::route('/{record}/edit'),
        ];
    }
}
