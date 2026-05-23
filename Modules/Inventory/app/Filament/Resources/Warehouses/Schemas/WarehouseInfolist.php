<?php

namespace Modules\Inventory\Filament\Resources\Warehouses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class WarehouseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->columns(2)
                ->schema([
                    TextEntry::make('code')->label(FilamentUi::field('code')),
                    TextEntry::make('name')->label(FilamentUi::field('name')),
                    TextEntry::make('organization.name')->label(FilamentUi::field('organization_id')),
                    TextEntry::make('address')->label(FilamentUi::field('address'))->columnSpanFull(),
                    IconEntry::make('is_default')->label(FilamentUi::field('is_default'))->boolean(),
                    IconEntry::make('is_active')->label(FilamentUi::field('is_active'))->boolean(),
                ]),
        ]);
    }
}
