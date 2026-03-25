<?php

namespace Modules\Monitoring\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant'))
                    ->placeholder('-'),
                TextEntry::make('user.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('User'))
                    ->placeholder('-'),
                TextEntry::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Organization'))
                    ->placeholder('-'),
                TextEntry::make('auditable_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('auditable_type'))
                    ->placeholder('-'),
                TextEntry::make('auditable_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('auditable_id'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('action')
                    ->label(\Modules\Core\Support\FilamentUi::field('action')),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('old_values')
                    ->label(\Modules\Core\Support\FilamentUi::field('old_values'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('new_values')
                    ->label(\Modules\Core\Support\FilamentUi::field('new_values'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('ip_address')
                    ->label(\Modules\Core\Support\FilamentUi::field('ip_address'))
                    ->placeholder('-'),
                TextEntry::make('user_agent')
                    ->label(\Modules\Core\Support\FilamentUi::field('user_agent'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('request_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('request_id'))
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('error_message')
                    ->label(\Modules\Core\Support\FilamentUi::field('error_message'))
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
