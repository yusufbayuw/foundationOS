<?php

namespace Modules\Campus\Filament\Resources\Faculties\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FacultyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Faculty Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('code')
                            ->label(\Modules\Core\Support\FilamentUi::field('code'))
                            ->placeholder('-'),
                        TextEntry::make('name')
                            ->label(\Modules\Core\Support\FilamentUi::field('name')),
                        TextEntry::make('short_name')
                            ->label(\Modules\Core\Support\FilamentUi::field('short_name'))
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->label(\Modules\Core\Support\FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Contact Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('office_phone')
                            ->label(\Modules\Core\Support\FilamentUi::field('office_phone'))
                            ->placeholder('-'),
                        TextEntry::make('office_email')
                            ->label(\Modules\Core\Support\FilamentUi::field('office_email'))
                            ->placeholder('-'),
                    ]),

                Section::make('Status')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_active')
                            ->boolean(),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
