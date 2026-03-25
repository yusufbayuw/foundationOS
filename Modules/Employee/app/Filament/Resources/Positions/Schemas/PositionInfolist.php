<?php

namespace Modules\Employee\Filament\Resources\Positions\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PositionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Organization')),
                TextEntry::make('department.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Department'))
                    ->placeholder('-'),
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('level')
                    ->label(\Modules\Core\Support\FilamentUi::field('level'))
                    ->numeric(),
                TextEntry::make('job_description')
                    ->label(\Modules\Core\Support\FilamentUi::field('job_description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('qualifications')
                    ->label(\Modules\Core\Support\FilamentUi::field('qualifications'))
                    ->placeholder('-')
                    ->columnSpanFull(),
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
