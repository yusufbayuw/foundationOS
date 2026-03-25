<?php

namespace Modules\Enrollment\Filament\Resources\Registrations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RegistrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('applicant.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Applicant')),
                TextEntry::make('completed_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('completed_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('registration_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('registration_date'))
                    ->date(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('payment_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_status')),
                TextEntry::make('total_fee')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_fee'))
                    ->numeric(),
                TextEntry::make('paid_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('paid_amount'))
                    ->numeric(),
                TextEntry::make('uniform_size')
                    ->label(\Modules\Core\Support\FilamentUi::field('uniform_size'))
                    ->placeholder('-'),
                TextEntry::make('documents_received')
                    ->label(\Modules\Core\Support\FilamentUi::field('documents_received'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('completed_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('completed_at'))
                    ->dateTime()
                    ->placeholder('-'),
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
