<?php

namespace Modules\Cms\Filament\Resources\Sites;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Cms\Filament\Resources\Sites\Pages\CreateSite;
use Modules\Cms\Filament\Resources\Sites\Pages\EditSite;
use Modules\Cms\Filament\Resources\Sites\Pages\ListSites;
use Modules\Cms\Filament\Resources\Sites\Pages\ViewSite;
use Modules\Cms\Filament\Resources\Sites\Schemas\SiteForm;
use Modules\Cms\Filament\Resources\Sites\Tables\SitesTable;
use Modules\Cms\Models\Site;
use Modules\Core\Filament\Support\ModuleResource;

class SiteResource extends ModuleResource
{
    protected static ?string $model = Site::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SiteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SitesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSites::route('/'),
            'create' => CreateSite::route('/create'),
            'view' => ViewSite::route('/{record}'),
            'edit' => EditSite::route('/{record}/edit'),
        ];
    }
}
