<?php

namespace Modules\Counseling\Filament\Resources\CounselingNotes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Counseling\Models\CounselingNote;

class CounselingNoteInfolist
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
                    ->visible(fn (CounselingNote $record): bool => $record->trashed()),
                TextEntry::make('counselingCase.name')
                    ->label('Counseling case')
                    ->placeholder('-'),
                IconEntry::make('is_confidential')
                    ->boolean(),
                TextEntry::make('body')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }
}
