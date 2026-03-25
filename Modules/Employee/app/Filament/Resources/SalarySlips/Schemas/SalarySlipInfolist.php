<?php

namespace Modules\Employee\Filament\Resources\SalarySlips\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SalarySlipInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('employee.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Employee')),
                TextEntry::make('period_month')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_month')),
                TextEntry::make('period_year')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_year')),
                TextEntry::make('period_label')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_label')),
                TextEntry::make('basic_salary')
                    ->label(\Modules\Core\Support\FilamentUi::field('basic_salary'))
                    ->numeric(),
                TextEntry::make('earnings_details')
                    ->label(\Modules\Core\Support\FilamentUi::field('earnings_details'))
                    ->columnSpanFull(),
                TextEntry::make('deductions_details')
                    ->label(\Modules\Core\Support\FilamentUi::field('deductions_details'))
                    ->columnSpanFull(),
                TextEntry::make('total_earnings')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_earnings'))
                    ->numeric(),
                TextEntry::make('total_deductions')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_deductions'))
                    ->numeric(),
                TextEntry::make('net_salary')
                    ->label(\Modules\Core\Support\FilamentUi::field('net_salary'))
                    ->numeric(),
                TextEntry::make('tax_details')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_details'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('bpjs_details')
                    ->label(\Modules\Core\Support\FilamentUi::field('bpjs_details'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('working_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('working_days'))
                    ->numeric(),
                TextEntry::make('working_hours')
                    ->label(\Modules\Core\Support\FilamentUi::field('working_hours'))
                    ->numeric(),
                TextEntry::make('overtime_hours')
                    ->label(\Modules\Core\Support\FilamentUi::field('overtime_hours'))
                    ->numeric(),
                TextEntry::make('leave_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('leave_days'))
                    ->numeric(),
                TextEntry::make('absent_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('absent_days'))
                    ->numeric(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('paid_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('paid_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('paid_via')
                    ->label(\Modules\Core\Support\FilamentUi::field('paid_via'))
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_sent')
                    ->boolean(),
                TextEntry::make('sent_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('sent_at'))
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
