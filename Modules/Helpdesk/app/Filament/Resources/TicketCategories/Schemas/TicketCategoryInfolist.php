<?php

namespace Modules\Helpdesk\Filament\Resources\TicketCategories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Helpdesk\Models\TicketCategory;

class TicketCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('organization.name')
                    ->label('Organization')
                    ->placeholder('-'),
                TextEntry::make('code')
                    ->placeholder('-'),
                TextEntry::make('name')
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('meta')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (TicketCategory $record): bool => $record->trashed()),
                TextEntry::make('response_hours')
                    ->numeric(),
                TextEntry::make('resolution_hours')
                    ->numeric(),
                TextEntry::make('default_assignee_user_id')
                    ->numeric()
                    ->placeholder('-'),
            ]);
    }
}
