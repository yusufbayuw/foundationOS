<?php

namespace Modules\Sales\Filament\Resources\Customers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('tenant_id')
                        ->label(FilamentUi::field('tenant_id'))
                        ->placeholder('-'),
                    TextEntry::make('organization_id')
                        ->label(FilamentUi::field('organization_id'))
                        ->placeholder('-'),
                    TextEntry::make('code')
                        ->label(FilamentUi::field('code'))
                        ->placeholder('-'),
                    TextEntry::make('name')
                        ->label(FilamentUi::field('name'))
                        ->placeholder('-'),
                    TextEntry::make('email')
                        ->label(FilamentUi::field('email'))
                        ->placeholder('-'),
                    TextEntry::make('phone')
                        ->label(FilamentUi::field('phone'))
                        ->placeholder('-'),
                    TextEntry::make('address')
                        ->label(FilamentUi::field('address'))
                        ->placeholder('-'),
                    TextEntry::make('is_active')
                        ->label(FilamentUi::field('is_active'))
                        ->placeholder('-'),
                    TextEntry::make('is_cooperative_member')
                        ->label(FilamentUi::field('is_cooperative_member'))
                        ->placeholder('-'),
                    TextEntry::make('member_number')
                        ->label(FilamentUi::field('member_number'))
                        ->placeholder('-'),
                    TextEntry::make('member_discount_percent')
                        ->label(FilamentUi::field('member_discount_percent'))
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
