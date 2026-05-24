<?php

namespace Modules\Facility\Filament\Resources\UtilityReadings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Facility\Models\UtilityReading;

class UtilityReadingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('organization.name')
                    ->label('Organization')
                    ->placeholder('-'),
                TextEntry::make('code')
                    ->placeholder('-'),
                TextEntry::make('name')
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('meta')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (UtilityReading $record): bool => $record->trashed()),
                TextEntry::make('building_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('utility_type')
                    ->placeholder('-'),
                TextEntry::make('period_month')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('reading_value')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('emission_factor')
                    ->numeric()
                    ->placeholder('-'),
            ]);
    }
}
