<?php

namespace Modules\Core\Filament\Resources\Stakeholders\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Models\Stakeholder;
use Modules\Core\Support\FilamentUi;

class StakeholderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(FilamentUi::text('Tenant')),
                TextEntry::make('organization.name')
                    ->label(FilamentUi::text('Organization'))
                    ->placeholder('-'),
                TextEntry::make('user.name')
                    ->label(FilamentUi::text('User'))
                    ->placeholder('-'),
                TextEntry::make('role_type'),
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label(FilamentUi::text('Email address'))
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('term_start')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('term_end')
                    ->date()
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Stakeholder $record): bool => $record->trashed()),
            ]);
    }
}
