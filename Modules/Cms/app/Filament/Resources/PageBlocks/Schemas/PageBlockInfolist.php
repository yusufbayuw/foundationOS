<?php

namespace Modules\Cms\Filament\Resources\PageBlocks\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PageBlockInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant_id')
                    ->numeric(),
                TextEntry::make('organization_id')
                    ->numeric()
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
                TextEntry::make('page.name')
                    ->label('Page')
                    ->placeholder('-'),
                TextEntry::make('block_type')
                    ->placeholder('-'),
                TextEntry::make('sort_order')
                    ->numeric(),
                TextEntry::make('content')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }
}
