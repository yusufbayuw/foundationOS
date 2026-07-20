<?php

namespace Modules\Cms\Filament\Resources\HomepageSections\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class HomepageSectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name'),
                TextEntry::make('type'),
                TextEntry::make('title'),
                TextEntry::make('sort_order'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('settings'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
