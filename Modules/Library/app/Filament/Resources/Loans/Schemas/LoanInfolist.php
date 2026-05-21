<?php

namespace Modules\Library\Filament\Resources\Loans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class LoanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Loan Parties')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('bookCopy.id')
                            ->label(FilamentUi::text('Book copy')),
                        TextEntry::make('member.id')
                            ->label(FilamentUi::text('Member')),
                        TextEntry::make('processed_by')
                            ->label(FilamentUi::field('processed_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('returned_by')
                            ->label(FilamentUi::field('returned_by'))
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                Section::make('Loan Period')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('loan_date')
                            ->label(FilamentUi::field('loan_date'))
                            ->date(),
                        TextEntry::make('due_date')
                            ->label(FilamentUi::field('due_date'))
                            ->date(),
                        TextEntry::make('return_date')
                            ->label(FilamentUi::field('return_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('extension_count')
                            ->label(FilamentUi::field('extension_count'))
                            ->numeric(),
                        TextEntry::make('max_extensions')
                            ->label(FilamentUi::field('max_extensions'))
                            ->numeric(),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                    ]),

                Section::make('Fines')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('fine_amount')
                            ->label(FilamentUi::field('fine_amount'))
                            ->numeric(),
                        TextEntry::make('fine_paid')
                            ->label(FilamentUi::field('fine_paid'))
                            ->numeric(),
                        TextEntry::make('fine_status')
                            ->label(FilamentUi::field('fine_status')),
                    ]),

                Section::make('Condition & Notes')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('condition_on_loan')
                            ->label(FilamentUi::field('condition_on_loan'))
                            ->placeholder('-'),
                        TextEntry::make('condition_on_return')
                            ->label(FilamentUi::field('condition_on_return'))
                            ->placeholder('-'),
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
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
