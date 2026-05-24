<?php

namespace Modules\Messaging\Filament\Resources\NotificationDeliveries;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Messaging\Filament\Resources\NotificationDeliveries\Pages\CreateNotificationDelivery;
use Modules\Messaging\Filament\Resources\NotificationDeliveries\Pages\EditNotificationDelivery;
use Modules\Messaging\Filament\Resources\NotificationDeliveries\Pages\ListNotificationDeliveries;
use Modules\Messaging\Filament\Resources\NotificationDeliveries\Pages\ViewNotificationDelivery;
use Modules\Messaging\Filament\Resources\NotificationDeliveries\Schemas\NotificationDeliveryForm;
use Modules\Messaging\Filament\Resources\NotificationDeliveries\Schemas\NotificationDeliveryInfolist;
use Modules\Messaging\Filament\Resources\NotificationDeliveries\Tables\NotificationDeliveriesTable;
use Modules\Messaging\Models\NotificationDelivery;

class NotificationDeliveryResource extends ModuleResource
{
    protected static ?string $model = NotificationDelivery::class;

    public static function form(Schema $schema): Schema
    {
        return NotificationDeliveryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return NotificationDeliveryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NotificationDeliveriesTable::configure($table);
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
            'index' => ListNotificationDeliveries::route('/'),
            'create' => CreateNotificationDelivery::route('/create'),
            'view' => ViewNotificationDelivery::route('/{record}'),
            'edit' => EditNotificationDelivery::route('/{record}/edit'),
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
