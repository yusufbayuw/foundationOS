<?php

namespace Modules\Employee\Filament\Resources\SalarySlips\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class SalarySlipInfolist
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
                        TextEntry::make('employee.id')
                            ->label(FilamentUi::text('Employee')),
                        TextEntry::make('period_month')
                            ->label(FilamentUi::field('period_month')),
                        TextEntry::make('period_year')
                            ->label(FilamentUi::field('period_year')),
                        TextEntry::make('period_label')
                            ->label(FilamentUi::field('period_label')),
                        TextEntry::make('basic_salary')
                            ->label(FilamentUi::field('basic_salary'))
                            ->numeric(),
                    ]),

                Section::make('Earnings & Deductions')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('earnings_details')
                            ->label(FilamentUi::field('earnings_details'))
                            ->columnSpanFull(),
                        TextEntry::make('deductions_details')
                            ->label(FilamentUi::field('deductions_details'))
                            ->columnSpanFull(),
                        TextEntry::make('total_earnings')
                            ->label(FilamentUi::field('total_earnings'))
                            ->numeric(),
                        TextEntry::make('total_deductions')
                            ->label(FilamentUi::field('total_deductions'))
                            ->numeric(),
                        TextEntry::make('net_salary')
                            ->label(FilamentUi::field('net_salary'))
                            ->numeric(),
                    ]),

                Section::make('Tax & Benefits')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tax_details')
                            ->label(FilamentUi::field('tax_details'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('bpjs_details')
                            ->label(FilamentUi::field('bpjs_details'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Attendance')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('working_days')
                            ->label(FilamentUi::field('working_days'))
                            ->numeric(),
                        TextEntry::make('working_hours')
                            ->label(FilamentUi::field('working_hours'))
                            ->numeric(),
                        TextEntry::make('overtime_hours')
                            ->label(FilamentUi::field('overtime_hours'))
                            ->numeric(),
                        TextEntry::make('leave_days')
                            ->label(FilamentUi::field('leave_days'))
                            ->numeric(),
                        TextEntry::make('absent_days')
                            ->label(FilamentUi::field('absent_days'))
                            ->numeric(),
                    ]),

                Section::make('Payment')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('paid_at')
                            ->label(FilamentUi::field('paid_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('paid_via')
                            ->label(FilamentUi::field('paid_via'))
                            ->placeholder('-'),
                        IconEntry::make('is_sent')
                            ->boolean(),
                        TextEntry::make('sent_at')
                            ->label(FilamentUi::field('sent_at'))
                            ->dateTime()
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
