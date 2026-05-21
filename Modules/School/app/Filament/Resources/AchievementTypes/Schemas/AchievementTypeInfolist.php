<?php

namespace Modules\School\Filament\Resources\AchievementTypes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class AchievementTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code')),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                    ]),

                Section::make('Classification')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('category')
                            ->label(FilamentUi::field('category'))
                            ->placeholder('-'),
                        TextEntry::make('level')
                            ->label(FilamentUi::field('level'))
                            ->placeholder('-'),
                        TextEntry::make('point_weight')
                            ->label(FilamentUi::field('point_weight'))
                            ->numeric(),
                        TextEntry::make('certificate_template')
                            ->label(FilamentUi::field('certificate_template'))
                            ->placeholder('-'),
                    ]),

                Section::make('Status')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_active')
                            ->boolean(),
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
