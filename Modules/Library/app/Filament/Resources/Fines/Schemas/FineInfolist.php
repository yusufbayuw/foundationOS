<?php

namespace Modules\Library\Filament\Resources\Fines\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FineInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('loan.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Loan')),
                TextEntry::make('fine_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('fine_type')),
                TextEntry::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->numeric(),
                TextEntry::make('paid_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('paid_amount'))
                    ->numeric(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('issued_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('issued_at'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('paid_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('paid_at'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
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
