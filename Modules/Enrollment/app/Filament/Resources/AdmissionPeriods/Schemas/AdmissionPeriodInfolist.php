<?php

namespace Modules\Enrollment\Filament\Resources\AdmissionPeriods\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AdmissionPeriodInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Organization')),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextEntry::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type'))
                    ->placeholder('-'),
                TextEntry::make('start_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_date'))
                    ->date(),
                TextEntry::make('end_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_date'))
                    ->date(),
                TextEntry::make('announcement_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('announcement_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('registration_fee')
                    ->label(\Modules\Core\Support\FilamentUi::field('registration_fee'))
                    ->numeric(),
                TextEntry::make('quota')
                    ->label(\Modules\Core\Support\FilamentUi::field('quota'))
                    ->numeric(),
                TextEntry::make('registered_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('registered_count'))
                    ->numeric(),
                TextEntry::make('accepted_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('accepted_count'))
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('requirements')
                    ->label(\Modules\Core\Support\FilamentUi::field('requirements'))
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
