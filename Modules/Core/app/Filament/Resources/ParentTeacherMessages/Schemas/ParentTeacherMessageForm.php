<?php

namespace Modules\Core\Filament\Resources\ParentTeacherMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ParentTeacherMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('student_id')
                    ->relationship('student', 'id')
                    ->required(),
                Select::make('parent_user_id')
                    ->relationship('parentUser', 'name')
                    ->required(),
                Select::make('teacher_user_id')
                    ->relationship('teacherUser', 'name')
                    ->required(),
                Textarea::make('body')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('sender_user_id')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('read_at'),
            ]);
    }
}
