<?php

namespace Modules\Monitoring\Filament\Resources\WebhookDeliveries;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Monitoring\Filament\Resources\WebhookDeliveries\Pages\CreateWebhookDelivery;
use Modules\Monitoring\Filament\Resources\WebhookDeliveries\Pages\EditWebhookDelivery;
use Modules\Monitoring\Filament\Resources\WebhookDeliveries\Pages\ListWebhookDeliveries;
use Modules\Monitoring\Filament\Resources\WebhookDeliveries\Pages\ViewWebhookDelivery;
use Modules\Monitoring\Filament\Resources\WebhookDeliveries\Schemas\WebhookDeliveryForm;
use Modules\Monitoring\Filament\Resources\WebhookDeliveries\Schemas\WebhookDeliveryInfolist;
use Modules\Monitoring\Filament\Resources\WebhookDeliveries\Tables\WebhookDeliveriesTable;
use Modules\Monitoring\Models\WebhookDelivery;

class WebhookDeliveryResource extends ModuleResource
{
    protected static ?string $model = WebhookDelivery::class;

    public static function form(Schema $schema): Schema
    {
        return WebhookDeliveryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WebhookDeliveryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WebhookDeliveriesTable::configure($table);
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
            'index' => ListWebhookDeliveries::route('/'),
            'create' => CreateWebhookDelivery::route('/create'),
            'view' => ViewWebhookDelivery::route('/{record}'),
            'edit' => EditWebhookDelivery::route('/{record}/edit'),
        ];
    }
}
