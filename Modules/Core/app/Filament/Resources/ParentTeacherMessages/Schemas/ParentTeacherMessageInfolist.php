<?php

namespace Modules\Core\Filament\Resources\ParentTeacherMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ParentTeacherMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(FilamentUi::text('Tenant')),
                TextEntry::make('student.id')
                    ->label(FilamentUi::text('Student')),
                TextEntry::make('parentUser.name')
                    ->label(FilamentUi::text('Parent user')),
                TextEntry::make('teacherUser.name')
                    ->label(FilamentUi::text('Teacher user')),
                TextEntry::make('body')
                    ->columnSpanFull(),
                TextEntry::make('sender_user_id')
                    ->numeric(),
                TextEntry::make('read_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
