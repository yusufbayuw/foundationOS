<?php

namespace Modules\Member\Filament\Resources\MemberTypes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;
use Modules\Member\Models\MemberType;

class MemberTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Member type'))
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('code')
                            ->copyable(),
                        TextEntry::make('members_count')
                            ->label(FilamentUi::text('Members'))
                            ->state(fn (MemberType $record): int => $record->members()->count()),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
