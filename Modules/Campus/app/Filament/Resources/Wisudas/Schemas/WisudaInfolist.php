<?php

namespace Modules\Campus\Filament\Resources\Wisudas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Campus\Models\Wisuda;
use Modules\Core\Support\FilamentUi;

class WisudaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(FilamentUi::text('Tenant')),
                TextEntry::make('yudisium.id')
                    ->label(FilamentUi::text('Yudisium'))
                    ->placeholder('-'),
                TextEntry::make('name'),
                TextEntry::make('held_at')
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
                    ->visible(fn (Wisuda $record): bool => $record->trashed()),
            ]);
    }
}
