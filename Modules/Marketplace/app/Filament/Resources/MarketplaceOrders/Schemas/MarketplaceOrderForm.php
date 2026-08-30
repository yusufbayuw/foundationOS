<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrders\Schemas;

use App\Enums\ShopOrderStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class MarketplaceOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TenantField::organizationSelect(),
                TextInput::make('code'),
                TextInput::make('name'),
                Select::make('status')
                    ->options(ShopOrderStatus::options())
                    ->required()
                    ->default(ShopOrderStatus::PendingPayment->value),
                Textarea::make('rejection_reason')
                    ->label(FilamentUi::text('Rejection reason'))
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('meta')
                    ->columnSpanFull(),
                Select::make('seller_id')
                    ->relationship('seller', 'name'),
            ]);
    }
}
