<?php

namespace Modules\Enrollment\Filament\Resources\AdmissionPeriods\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class AdmissionPeriodInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization')),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code')),
                        TextEntry::make('type')
                            ->label(FilamentUi::field('type'))
                            ->placeholder('-'),
                        IconEntry::make('is_active')
                            ->boolean(),
                    ]),

                Section::make(FilamentUi::text('Schedule'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('start_date')
                            ->label(FilamentUi::field('start_date'))
                            ->date(),
                        TextEntry::make('end_date')
                            ->label(FilamentUi::field('end_date'))
                            ->date(),
                        TextEntry::make('announcement_date')
                            ->label(FilamentUi::field('announcement_date'))
                            ->date()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Capacity & Fees'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('registration_fee')
                            ->label(FilamentUi::field('registration_fee'))
                            ->numeric(),
                        TextEntry::make('quota')
                            ->label(FilamentUi::field('quota'))
                            ->numeric(),
                        TextEntry::make('registered_count')
                            ->label(FilamentUi::field('registered_count'))
                            ->numeric(),
                        TextEntry::make('accepted_count')
                            ->label(FilamentUi::field('accepted_count'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('requirements')
                            ->label(FilamentUi::field('requirements'))
                            ->placeholder('-')
                            ->columnSpanFull(),
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
