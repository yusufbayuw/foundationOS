<?php

namespace Modules\Employee\Filament\Resources\KpiTemplates\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class KpiTemplateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Template Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization')),
                        TextEntry::make('department.name')
                            ->label(FilamentUi::text('Department'))
                            ->placeholder('-'),
                        TextEntry::make('position.name')
                            ->label(FilamentUi::text('Position'))
                            ->placeholder('-'),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                    ]),

                Section::make(FilamentUi::text('Indicators & Weight'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('indicators')
                            ->label(FilamentUi::field('indicators'))
                            ->columnSpanFull(),
                        TextEntry::make('total_weight')
                            ->label(FilamentUi::field('total_weight'))
                            ->numeric(),
                        IconEntry::make('is_active')
                            ->boolean(),
                    ]),

                Section::make(FilamentUi::text('Timestamps'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
