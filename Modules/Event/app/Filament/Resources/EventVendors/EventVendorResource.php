<?php

namespace Modules\Event\Filament\Resources\EventVendors;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Event\Filament\Resources\EventVendors\Pages\CreateEventVendor;
use Modules\Event\Filament\Resources\EventVendors\Pages\EditEventVendor;
use Modules\Event\Filament\Resources\EventVendors\Pages\ListEventVendors;
use Modules\Event\Filament\Resources\EventVendors\Pages\ViewEventVendor;
use Modules\Event\Filament\Resources\EventVendors\Schemas\EventVendorForm;
use Modules\Event\Filament\Resources\EventVendors\Schemas\EventVendorInfolist;
use Modules\Event\Filament\Resources\EventVendors\Tables\EventVendorsTable;
use Modules\Event\Models\EventVendor;

class EventVendorResource extends ModuleResource
{
    protected static ?string $model = EventVendor::class;

    public static function form(Schema $schema): Schema
    {
        return EventVendorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EventVendorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventVendorsTable::configure($table);
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
            'index' => ListEventVendors::route('/'),
            'create' => CreateEventVendor::route('/create'),
            'view' => ViewEventVendor::route('/{record}'),
            'edit' => EditEventVendor::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
