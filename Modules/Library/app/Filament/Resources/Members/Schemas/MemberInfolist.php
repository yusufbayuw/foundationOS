<?php

namespace Modules\Library\Filament\Resources\Members\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class MemberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Scope & Identity'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('user.name')
                            ->label(FilamentUi::text('User')),
                        TextEntry::make('member_number')
                            ->label(FilamentUi::field('member_number')),
                    ]),

                Section::make(FilamentUi::text('Membership'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('member_type')
                            ->label(FilamentUi::field('member_type'))
                            ->placeholder('-'),
                        TextEntry::make('joined_at')
                            ->label(FilamentUi::field('joined_at'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('expires_at')
                            ->label(FilamentUi::field('expires_at'))
                            ->date()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Membership Evidence & Profile'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('membership_proof')
                            ->label(FilamentUi::text('Membership Proof'))
                            ->placeholder('-'),
                        TextEntry::make('profile_data')
                            ->label(FilamentUi::text('Profile Data'))
                            ->state(fn ($record): string => json_encode($record->profile_data ?? [], JSON_PRETTY_PRINT))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Loan Limits'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('max_books')
                            ->label(FilamentUi::field('max_books'))
                            ->numeric(),
                        TextEntry::make('loan_period_days')
                            ->label(FilamentUi::field('loan_period_days'))
                            ->numeric(),
                        TextEntry::make('fine_per_day')
                            ->label(FilamentUi::field('fine_per_day'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Activity & Fines'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('total_loans_count')
                            ->label(FilamentUi::field('total_loans_count'))
                            ->numeric(),
                        TextEntry::make('current_loans_count')
                            ->label(FilamentUi::field('current_loans_count'))
                            ->numeric(),
                        TextEntry::make('total_fines')
                            ->label(FilamentUi::field('total_fines'))
                            ->numeric(),
                        TextEntry::make('unpaid_fines')
                            ->label(FilamentUi::field('unpaid_fines'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Status & Notes'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('verified_at')
                            ->label(FilamentUi::field('verified_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('verifiedBy.name')
                            ->label(FilamentUi::field('verified_by'))
                            ->placeholder('-'),
                        TextEntry::make('rejection_reason')
                            ->label(FilamentUi::field('rejection_reason'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('suspension_until')
                            ->label(FilamentUi::field('suspension_until'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('suspension_reason')
                            ->label(FilamentUi::field('suspension_reason'))
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
