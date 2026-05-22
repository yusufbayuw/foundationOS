<?php

namespace Modules\Monitoring\Filament\Resources\WebhookSubscriptions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Monitoring\Filament\Resources\WebhookSubscriptions\Pages\CreateWebhookSubscription;
use Modules\Monitoring\Filament\Resources\WebhookSubscriptions\Pages\EditWebhookSubscription;
use Modules\Monitoring\Filament\Resources\WebhookSubscriptions\Pages\ListWebhookSubscriptions;
use Modules\Monitoring\Filament\Resources\WebhookSubscriptions\Schemas\WebhookSubscriptionForm;
use Modules\Monitoring\Filament\Resources\WebhookSubscriptions\Tables\WebhookSubscriptionsTable;
use Modules\Monitoring\Models\WebhookSubscription;

class WebhookSubscriptionResource extends LocalizedResource
{
    protected static ?string $model = WebhookSubscription::class;

    protected static ?string $recordTitleAttribute = 'url';

    public static function form(Schema $schema): Schema
    {
        return WebhookSubscriptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WebhookSubscriptionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWebhookSubscriptions::route('/'),
            'create' => CreateWebhookSubscription::route('/create'),
            'edit' => EditWebhookSubscription::route('/{record}/edit'),
        ];
    }
}
