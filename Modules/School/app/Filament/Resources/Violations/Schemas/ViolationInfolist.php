<?php

namespace Modules\School\Filament\Resources\Violations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ViolationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('student.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Student')),
                TextEntry::make('violationType.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Violation type'))
                    ->placeholder('-'),
                TextEntry::make('reported_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('reported_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('handled_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('handled_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('date')
                    ->label(\Modules\Core\Support\FilamentUi::field('date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('severity')
                    ->label(\Modules\Core\Support\FilamentUi::field('severity'))
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('location')
                    ->label(\Modules\Core\Support\FilamentUi::field('location'))
                    ->placeholder('-'),
                TextEntry::make('witnesses')
                    ->label(\Modules\Core\Support\FilamentUi::field('witnesses'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('sanctions')
                    ->label(\Modules\Core\Support\FilamentUi::field('sanctions'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('sanction_duration_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('sanction_duration_days'))
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('parent_notified')
                    ->boolean(),
                TextEntry::make('parent_meeting_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('parent_meeting_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('resolution_notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('resolution_notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->placeholder('-'),
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
