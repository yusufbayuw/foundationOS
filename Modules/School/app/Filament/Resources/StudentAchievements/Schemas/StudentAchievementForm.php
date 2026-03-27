<?php

namespace Modules\School\Filament\Resources\StudentAchievements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class StudentAchievementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name'),
                Select::make('academic_year_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('academic_year_id'))
                    ->relationship('academicYear', 'name'),
                Select::make('student_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('student_id'))
                    ->relationship('student', 'id')
                    ->required(),
                Select::make('achievement_type_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('achievement_type_id'))
                    ->relationship('achievementType', 'name'),
                TextInput::make('verified_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('verified_by'))
                    ->numeric(),
                TextInput::make('title')
                    ->label(\Modules\Core\Support\FilamentUi::field('title'))
                    ->required(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                TextInput::make('event_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('event_name')),
                DatePicker::make('event_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('event_date')),
                TextInput::make('event_location')
                    ->label(\Modules\Core\Support\FilamentUi::field('event_location')),
                TextInput::make('organizer')
                    ->label(\Modules\Core\Support\FilamentUi::field('organizer')),
                TextInput::make('rank_position')
                    ->label(\Modules\Core\Support\FilamentUi::field('rank_position')),
                TextInput::make('certificate_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('certificate_number')),
                TextInput::make('certificate_file')
                    ->label(\Modules\Core\Support\FilamentUi::field('certificate_file')),
                Textarea::make('photo_files')
                    ->label(\Modules\Core\Support\FilamentUi::field('photo_files'))
                    ->columnSpanFull(),
                TextInput::make('news_link')
                    ->label(\Modules\Core\Support\FilamentUi::field('news_link')),
                TextInput::make('points_earned')
                    ->label(\Modules\Core\Support\FilamentUi::field('points_earned'))
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('verified_at'),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
                Toggle::make('is_featured')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_featured'))
                    ->required(),
            ]);
    }
}
