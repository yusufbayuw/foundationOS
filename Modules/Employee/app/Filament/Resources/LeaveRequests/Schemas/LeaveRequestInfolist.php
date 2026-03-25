<?php

namespace Modules\Employee\Filament\Resources\LeaveRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LeaveRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('employee.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Employee')),
                TextEntry::make('substituteEmployee.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Substitute employee'))
                    ->placeholder('-'),
                TextEntry::make('supervisor.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Supervisor'))
                    ->placeholder('-'),
                TextEntry::make('approver.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Approver'))
                    ->placeholder('-'),
                TextEntry::make('leave_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('leave_type'))
                    ->placeholder('-'),
                TextEntry::make('start_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_date'))
                    ->date(),
                TextEntry::make('end_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_date'))
                    ->date(),
                TextEntry::make('total_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_days'))
                    ->numeric(),
                TextEntry::make('reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('reason'))
                    ->columnSpanFull(),
                TextEntry::make('attachment')
                    ->label(\Modules\Core\Support\FilamentUi::field('attachment'))
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('supervisor_approved_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('supervisor_approved_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('approved_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('rejection_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('rejection_reason'))
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
