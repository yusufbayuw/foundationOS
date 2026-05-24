<?php

namespace Modules\Core\Filament\Resources\ParentTeacherMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ParentTeacherMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('student.id')
                    ->label('Student'),
                TextEntry::make('parentUser.name')
                    ->label('Parent user'),
                TextEntry::make('teacherUser.name')
                    ->label('Teacher user'),
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
