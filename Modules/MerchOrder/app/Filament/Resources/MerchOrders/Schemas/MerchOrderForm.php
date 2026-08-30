<?php

namespace Modules\MerchOrder\Filament\Resources\MerchOrders\Schemas;

use App\Enums\ShopOrderStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class MerchOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))->schema([
                TextInput::make('code')->label(FilamentUi::field('code')),
                TextInput::make('name')->label(FilamentUi::field('name'))->required(),
                Select::make('status')->label(FilamentUi::field('status'))->options([
                    ...ShopOrderStatus::options(),
                ])->default(ShopOrderStatus::PendingPayment->value),
                Textarea::make('rejection_reason')
                    ->label(FilamentUi::text('Rejection reason'))
                    ->columnSpanFull(),
                Textarea::make('description')->label(FilamentUi::field('description'))->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
