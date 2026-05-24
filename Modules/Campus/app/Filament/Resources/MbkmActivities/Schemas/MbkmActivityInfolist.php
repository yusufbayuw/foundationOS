<?php

namespace Modules\Campus\Filament\Resources\MbkmActivities\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Campus\Models\MbkmActivity;

class MbkmActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('collageStudent.id')
                    ->label('Collage student')
                    ->placeholder('-'),
                TextEntry::make('activity_type'),
                TextEntry::make('title'),
                TextEntry::make('sks_credited')
                    ->numeric(),
                TextEntry::make('start_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('end_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (MbkmActivity $record): bool => $record->trashed()),
            ]);
    }
}
