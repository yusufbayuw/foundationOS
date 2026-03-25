<?php

namespace Modules\Finance\Filament\Resources\ChartOfAccounts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ChartOfAccountInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Organization')),
                TextEntry::make('parent.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Parent'))
                    ->placeholder('-'),
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('level')
                    ->label(\Modules\Core\Support\FilamentUi::field('level'))
                    ->numeric(),
                TextEntry::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type'))
                    ->placeholder('-'),
                TextEntry::make('category')
                    ->label(\Modules\Core\Support\FilamentUi::field('category'))
                    ->placeholder('-'),
                TextEntry::make('normal_balance')
                    ->label(\Modules\Core\Support\FilamentUi::field('normal_balance'))
                    ->placeholder('-'),
                IconEntry::make('is_bank_account')
                    ->boolean(),
                TextEntry::make('bank_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_name'))
                    ->placeholder('-'),
                TextEntry::make('bank_account_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_account_number'))
                    ->placeholder('-'),
                TextEntry::make('bank_account_holder')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_account_holder'))
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                IconEntry::make('is_locked')
                    ->boolean(),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('opening_balance')
                    ->label(\Modules\Core\Support\FilamentUi::field('opening_balance'))
                    ->numeric(),
                TextEntry::make('current_balance')
                    ->label(\Modules\Core\Support\FilamentUi::field('current_balance'))
                    ->numeric(),
                TextEntry::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
