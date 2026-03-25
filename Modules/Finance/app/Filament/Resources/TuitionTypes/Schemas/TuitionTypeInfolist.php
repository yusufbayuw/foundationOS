<?php

namespace Modules\Finance\Filament\Resources\TuitionTypes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TuitionTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Organization')),
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->placeholder('-'),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('education_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('education_level'))
                    ->placeholder('-'),
                TextEntry::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('frequency')
                    ->label(\Modules\Core\Support\FilamentUi::field('frequency'))
                    ->placeholder('-'),
                TextEntry::make('due_day')
                    ->label(\Modules\Core\Support\FilamentUi::field('due_day'))
                    ->numeric(),
                TextEntry::make('grace_period_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('grace_period_days'))
                    ->numeric(),
                TextEntry::make('late_fee_percentage')
                    ->label(\Modules\Core\Support\FilamentUi::field('late_fee_percentage'))
                    ->numeric(),
                TextEntry::make('late_fee_fixed')
                    ->label(\Modules\Core\Support\FilamentUi::field('late_fee_fixed'))
                    ->numeric(),
                IconEntry::make('discount_eligible')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
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
