<?php

namespace Modules\School\Filament\Resources\ClassStudents\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ClassStudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('academic_period_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('academic_period_id'))
                    ->relationship('academicPeriod', 'name'),
                TextInput::make('class_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('class_id'))
                    ->required()
                    ->numeric(),
                Select::make('student_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('student_id'))
                    ->relationship('student', 'id')
                    ->required(),
                DatePicker::make('entry_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_date')),
                DatePicker::make('exit_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('exit_date')),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextInput::make('entry_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_type')),
                Textarea::make('exit_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('exit_reason'))
                    ->columnSpanFull(),
                TextInput::make('ranking')
                    ->label(\Modules\Core\Support\FilamentUi::field('ranking'))
                    ->numeric(),
                TextInput::make('certificate_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('certificate_number'))
            ]);
    }
}
