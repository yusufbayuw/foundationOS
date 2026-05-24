<?php

namespace Modules\Core\Filament\Resources\ParentStudents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ParentStudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('parent_user_id')
                    ->relationship('parentUser', 'name')
                    ->required(),
                Select::make('student_id')
                    ->relationship('student', 'id')
                    ->required(),
                TextInput::make('relationship')
                    ->required()
                    ->default('guardian'),
                Toggle::make('is_primary')
                    ->required(),
            ]);
    }
}
