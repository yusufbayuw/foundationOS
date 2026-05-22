<?php

namespace Modules\Enrollment\Filament\Resources\Registrations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class RegistrationInfolist
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
                        TextEntry::make('applicant.id')
                            ->label(FilamentUi::text('Applicant')),
                        TextEntry::make('registration_date')
                            ->label(FilamentUi::field('registration_date'))
                            ->date(),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('completed_by')
                            ->label(FilamentUi::field('completed_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('completed_at')
                            ->label(FilamentUi::field('completed_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Payment'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('payment_status')
                            ->label(FilamentUi::field('payment_status')),
                        TextEntry::make('total_fee')
                            ->label(FilamentUi::field('total_fee'))
                            ->numeric(),
                        TextEntry::make('paid_amount')
                            ->label(FilamentUi::field('paid_amount'))
                            ->numeric(),
                        TextEntry::make('uniform_size')
                            ->label(FilamentUi::field('uniform_size'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Documents & Notes'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('documents_received')
                            ->label(FilamentUi::field('documents_received'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
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
