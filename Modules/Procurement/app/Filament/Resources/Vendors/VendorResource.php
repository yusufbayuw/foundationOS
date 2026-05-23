<?php

namespace Modules\Procurement\Filament\Resources\Vendors;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Filament\Concerns\ConfiguresGlobalSearch;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Procurement\Filament\Resources\Vendors\Pages\CreateVendor;
use Modules\Procurement\Filament\Resources\Vendors\Pages\EditVendor;
use Modules\Procurement\Filament\Resources\Vendors\Pages\ListVendors;
use Modules\Procurement\Filament\Resources\Vendors\Pages\ViewVendor;
use Modules\Procurement\Filament\Resources\Vendors\Schemas\VendorForm;
use Modules\Procurement\Filament\Resources\Vendors\Schemas\VendorInfolist;
use Modules\Procurement\Filament\Resources\Vendors\Tables\VendorsTable;
use Modules\Procurement\Models\Vendor;

class VendorResource extends LocalizedResource
{
    use ConfiguresGlobalSearch;

    protected static ?string $model = Vendor::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static function globalSearchAttributes(): array
    {
        return ['name', 'code'];
    }

    protected static function globalSearchResultDetails(Model $record): array
    {
        return static::detailStatus($record->status ?? null);
    }

    public static function form(Schema $schema): Schema
    {
        return VendorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VendorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorsTable::configure($table);
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
            'index' => ListVendors::route('/'),
            'create' => CreateVendor::route('/create'),
            'view' => ViewVendor::route('/{record}'),
            'edit' => EditVendor::route('/{record}/edit'),
        ];
    }
}
