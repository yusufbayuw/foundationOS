<?php

namespace Modules\Voucher\Filament\Resources\Vouchers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VoucherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Voucher')
                ->columns(2)
                ->schema([
                    TextEntry::make('tenant.name'),
                    TextEntry::make('title'),
                    TextEntry::make('code'),
                    TextEntry::make('status'),
                    TextEntry::make('quota')->numeric(),
                    TextEntry::make('claimed_count')->numeric(),
                    TextEntry::make('start_at')->dateTime(),
                    TextEntry::make('end_at')->dateTime(),
                    TextEntry::make('redemption_method'),
                    TextEntry::make('description')->columnSpanFull(),
                    TextEntry::make('terms')->columnSpanFull(),
                ]),
        ]);
    }
}
