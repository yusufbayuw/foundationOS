<?php

namespace Modules\Core\Filament\Resources\ParentStudents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class ParentStudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
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
