<?php

namespace Modules\Procurement\Filament\Resources\RfqVendors;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Procurement\Filament\Resources\RfqVendors\Pages\CreateRfqVendor;
use Modules\Procurement\Filament\Resources\RfqVendors\Pages\EditRfqVendor;
use Modules\Procurement\Filament\Resources\RfqVendors\Pages\ListRfqVendors;
use Modules\Procurement\Filament\Resources\RfqVendors\Pages\ViewRfqVendor;
use Modules\Procurement\Filament\Resources\RfqVendors\Schemas\RfqVendorForm;
use Modules\Procurement\Filament\Resources\RfqVendors\Schemas\RfqVendorInfolist;
use Modules\Procurement\Filament\Resources\RfqVendors\Tables\RfqVendorsTable;
use Modules\Procurement\Models\RfqVendor;

class RfqVendorResource extends LocalizedResource
{
    protected static ?string $model = RfqVendor::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RfqVendorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RfqVendorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RfqVendorsTable::configure($table);
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
            'index' => ListRfqVendors::route('/'),
            'create' => CreateRfqVendor::route('/create'),
            'view' => ViewRfqVendor::route('/{record}'),
            'edit' => EditRfqVendor::route('/{record}/edit'),
        ];
    }
}
