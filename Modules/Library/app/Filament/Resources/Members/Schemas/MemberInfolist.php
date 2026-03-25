<?php

namespace Modules\Library\Filament\Resources\Members\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MemberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('user.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('User')),
                TextEntry::make('member_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('member_number')),
                TextEntry::make('member_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('member_type'))
                    ->placeholder('-'),
                TextEntry::make('joined_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('joined_at'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('expires_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('expires_at'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('max_books')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_books'))
                    ->numeric(),
                TextEntry::make('loan_period_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('loan_period_days'))
                    ->numeric(),
                TextEntry::make('fine_per_day')
                    ->label(\Modules\Core\Support\FilamentUi::field('fine_per_day'))
                    ->numeric(),
                TextEntry::make('total_loans_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_loans_count'))
                    ->numeric(),
                TextEntry::make('current_loans_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('current_loans_count'))
                    ->numeric(),
                TextEntry::make('total_fines')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_fines'))
                    ->numeric(),
                TextEntry::make('unpaid_fines')
                    ->label(\Modules\Core\Support\FilamentUi::field('unpaid_fines'))
                    ->numeric(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('suspension_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('suspension_reason'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('suspension_until')
                    ->label(\Modules\Core\Support\FilamentUi::field('suspension_until'))
                    ->date()
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
