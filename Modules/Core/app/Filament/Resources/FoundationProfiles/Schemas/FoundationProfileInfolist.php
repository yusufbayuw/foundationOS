<?php

namespace Modules\Core\Filament\Resources\FoundationProfiles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class FoundationProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(FilamentUi::text('Tenant')),
                TextEntry::make('vision')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('mission')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('core_values')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('logo_path')
                    ->placeholder('-'),
                TextEntry::make('profile_document_path')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
