<?php

namespace Modules\Core\Filament\Resources\Modules\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ModuleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextEntry::make('slug')
                    ->label(\Modules\Core\Support\FilamentUi::field('slug')),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('icon')
                    ->label(\Modules\Core\Support\FilamentUi::field('icon'))
                    ->placeholder('-'),
                TextEntry::make('color')
                    ->label(\Modules\Core\Support\FilamentUi::field('color'))
                    ->placeholder('-'),
                TextEntry::make('version')
                    ->label(\Modules\Core\Support\FilamentUi::field('version'))
                    ->placeholder('-'),
                IconEntry::make('is_core')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                IconEntry::make('is_premium')
                    ->boolean(),
                TextEntry::make('price_monthly')
                    ->label(\Modules\Core\Support\FilamentUi::field('price_monthly'))
                    ->numeric(),
                TextEntry::make('price_yearly')
                    ->label(\Modules\Core\Support\FilamentUi::field('price_yearly'))
                    ->numeric(),
                TextEntry::make('settings_schema')
                    ->label(\Modules\Core\Support\FilamentUi::field('settings_schema'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('required_modules')
                    ->label(\Modules\Core\Support\FilamentUi::field('required_modules'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('sort_order')
                    ->label(\Modules\Core\Support\FilamentUi::field('sort_order'))
                    ->numeric(),
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
