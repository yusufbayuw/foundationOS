<?php

namespace Modules\Employee\Filament\Resources\LeaveRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class LeaveRequestInfolist
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
                        TextEntry::make('substituteEmployee.id')
                            ->label(FilamentUi::text('Substitute employee'))
                            ->placeholder('-'),
                        TextEntry::make('supervisor.name')
                            ->label(FilamentUi::text('Supervisor'))
                            ->placeholder('-'),
                        TextEntry::make('approver.name')
                            ->label(FilamentUi::text('Approver'))
                            ->placeholder('-'),
                        TextEntry::make('leave_type')
                            ->label(FilamentUi::field('leave_type'))
                            ->placeholder('-'),
                    ]),

                Section::make('Leave Period')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('start_date')
                            ->label(FilamentUi::field('start_date'))
                            ->date(),
                        TextEntry::make('end_date')
                            ->label(FilamentUi::field('end_date'))
                            ->date(),
                        TextEntry::make('total_days')
                            ->label(FilamentUi::field('total_days'))
                            ->numeric(),
                        TextEntry::make('attachment')
                            ->label(FilamentUi::field('attachment'))
                            ->placeholder('-'),
                        TextEntry::make('reason')
                            ->label(FilamentUi::field('reason'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Approval')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('supervisor_approved_at')
                            ->label(FilamentUi::field('supervisor_approved_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('approved_at')
                            ->label(FilamentUi::field('approved_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('rejection_reason')
                            ->label(FilamentUi::field('rejection_reason'))
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
