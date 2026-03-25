<?php

namespace Modules\Employee\Filament\Resources\Shifts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ShiftInfolist
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
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('start_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_time'))
                    ->time(),
                TextEntry::make('end_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_time'))
                    ->time(),
                TextEntry::make('break_duration_minutes')
                    ->label(\Modules\Core\Support\FilamentUi::field('break_duration_minutes'))
                    ->numeric(),
                TextEntry::make('color')
                    ->label(\Modules\Core\Support\FilamentUi::field('color'))
                    ->placeholder('-'),
                IconEntry::make('is_night_shift')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
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
