<?php

namespace Modules\Library\Filament\Resources\LibraryPolicies\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LibraryPolicyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Scope')
                ->columns(2)
                ->schema([
                    TextEntry::make('tenant.name'),
                    TextEntry::make('organization.name'),
                    TextEntry::make('name'),
                ]),

            Section::make('Loan Limits')
                ->columns(2)
                ->schema([
                    TextEntry::make('max_books'),
                    TextEntry::make('loan_period_days'),
                    TextEntry::make('fine_per_day'),
                    TextEntry::make('max_extensions'),
                    TextEntry::make('grace_period_days'),
                    TextEntry::make('reservation_pickup_days'),
                ]),

            Section::make('Notes')
                ->columns(2)
                ->schema([
                    TextEntry::make('notes'),
                ]),
        ]);
    }
}
