<?php

namespace Modules\Campus\Filament\Resources\CourseOfferings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class CourseOfferingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name'),
                Select::make('course_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('course_id'))
                    ->relationship('course', 'name')
                    ->required(),
                Select::make('academic_period_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('academic_period_id'))
                    ->relationship('academicPeriod', 'name'),
                Select::make('lecturer_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('lecturer_id'))
                    ->relationship('lecturer', 'id'),
                TextInput::make('class_code')
                    ->label(\Modules\Core\Support\FilamentUi::field('class_code'))
                    ->required(),
                TextInput::make('capacity')
                    ->label(\Modules\Core\Support\FilamentUi::field('capacity'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('enrolled_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('enrolled_count'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('delivery_mode')
                    ->label(\Modules\Core\Support\FilamentUi::field('delivery_mode'))
                    ->required()
                    ->default('offline'),
                TextInput::make('day_of_week')
                    ->label(\Modules\Core\Support\FilamentUi::field('day_of_week')),
                TextInput::make('start_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_time')),
                TextInput::make('end_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_time')),
                TextInput::make('room_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('room_name')),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('draft'),
            ]);
    }
}
