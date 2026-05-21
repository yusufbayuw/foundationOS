<?php

namespace Modules\Employee\Filament\Resources\Positions\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class PositionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization')),
                        TextEntry::make('department.name')
                            ->label(FilamentUi::text('Department'))
                            ->placeholder('-'),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code')),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('level')
                            ->label(FilamentUi::field('level'))
                            ->numeric(),
                    ]),

                Section::make('Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('job_description')
                            ->label(FilamentUi::field('job_description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('qualifications')
                            ->label(FilamentUi::field('qualifications'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_active')
                            ->boolean(),
                    ]),

                Section::make('Timestamps')
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
