<?php

namespace Modules\Employee\Filament\Resources\KpiTemplates\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KpiTemplateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Template Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Organization')),
                        TextEntry::make('department.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Department'))
                            ->placeholder('-'),
                        TextEntry::make('position.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Position'))
                            ->placeholder('-'),
                        TextEntry::make('name')
                            ->label(\Modules\Core\Support\FilamentUi::field('name')),
                    ]),

                Section::make('Indicators & Weight')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('indicators')
                            ->label(\Modules\Core\Support\FilamentUi::field('indicators'))
                            ->columnSpanFull(),
                        TextEntry::make('total_weight')
                            ->label(\Modules\Core\Support\FilamentUi::field('total_weight'))
                            ->numeric(),
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
