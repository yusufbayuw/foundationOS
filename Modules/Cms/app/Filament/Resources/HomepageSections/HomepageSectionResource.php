<?php

namespace Modules\Cms\Filament\Resources\HomepageSections;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Cms\Filament\Resources\HomepageSections\Pages\CreateHomepageSection;
use Modules\Cms\Filament\Resources\HomepageSections\Pages\EditHomepageSection;
use Modules\Cms\Filament\Resources\HomepageSections\Pages\ListHomepageSections;
use Modules\Cms\Filament\Resources\HomepageSections\Pages\ViewHomepageSection;
use Modules\Cms\Filament\Resources\HomepageSections\Schemas\HomepageSectionForm;
use Modules\Cms\Filament\Resources\HomepageSections\Schemas\HomepageSectionInfolist;
use Modules\Cms\Filament\Resources\HomepageSections\Tables\HomepageSectionsTable;
use Modules\Cms\Models\HomepageSection;
use Modules\Core\Filament\Support\ModuleResource;

class HomepageSectionResource extends ModuleResource
{
    protected static ?string $model = HomepageSection::class;

    public static function form(Schema $schema): Schema
    {
        return HomepageSectionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HomepageSectionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomepageSectionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHomepageSections::route('/'),
            'create' => CreateHomepageSection::route('/create'),
            'view' => ViewHomepageSection::route('/{record}'),
            'edit' => EditHomepageSection::route('/{record}/edit'),
        ];
    }
}
