<?php

namespace Modules\Asset\Filament\Resources\AssetLoans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Asset\Models\AssetLoan;

class AssetLoanInfolist
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
                    ->visible(fn (AssetLoan $record): bool => $record->trashed()),
                TextEntry::make('asset_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('borrower_user_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('loaned_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('returned_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
