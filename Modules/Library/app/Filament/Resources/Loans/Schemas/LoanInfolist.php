<?php

namespace Modules\Library\Filament\Resources\Loans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LoanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('bookCopy.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Book copy')),
                TextEntry::make('member.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Member')),
                TextEntry::make('processed_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('processed_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('returned_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('returned_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('loan_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('loan_date'))
                    ->date(),
                TextEntry::make('due_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('due_date'))
                    ->date(),
                TextEntry::make('return_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('return_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('extension_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('extension_count'))
                    ->numeric(),
                TextEntry::make('max_extensions')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_extensions'))
                    ->numeric(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('fine_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('fine_amount'))
                    ->numeric(),
                TextEntry::make('fine_paid')
                    ->label(\Modules\Core\Support\FilamentUi::field('fine_paid'))
                    ->numeric(),
                TextEntry::make('fine_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('fine_status')),
                TextEntry::make('condition_on_loan')
                    ->label(\Modules\Core\Support\FilamentUi::field('condition_on_loan'))
                    ->placeholder('-'),
                TextEntry::make('condition_on_return')
                    ->label(\Modules\Core\Support\FilamentUi::field('condition_on_return'))
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
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
