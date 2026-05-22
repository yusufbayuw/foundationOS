<?php

namespace Modules\School\Filament\Resources\StudentAchievements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class StudentAchievementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('academic_year_id')
                            ->label(FilamentUi::field('academic_year_id'))
                            ->relationship('academicYear', 'name'),
                        Select::make('student_id')
                            ->label(FilamentUi::field('student_id'))
                            ->relationship('student', 'id')
                            ->required(),
                        Select::make('achievement_type_id')
                            ->label(FilamentUi::field('achievement_type_id'))
                            ->relationship('achievementType', 'name'),
                        TextInput::make('verified_by')
                            ->label(FilamentUi::field('verified_by'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Achievement Details'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label(FilamentUi::field('title'))
                            ->required(),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                        TextInput::make('event_name')
                            ->label(FilamentUi::field('event_name')),
                        DatePicker::make('event_date')
                            ->label(FilamentUi::field('event_date')),
                        TextInput::make('event_location')
                            ->label(FilamentUi::field('event_location')),
                        TextInput::make('organizer')
                            ->label(FilamentUi::field('organizer')),
                        TextInput::make('rank_position')
                            ->label(FilamentUi::field('rank_position')),
                    ]),

                Section::make(FilamentUi::text('Certificate & Media'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('certificate_number')
                            ->label(FilamentUi::field('certificate_number')),
                        TextInput::make('certificate_file')
                            ->label(FilamentUi::field('certificate_file')),
                        Textarea::make('photo_files')
                            ->label(FilamentUi::field('photo_files'))
                            ->columnSpanFull(),
                        TextInput::make('news_link')
                            ->label(FilamentUi::field('news_link')),
                    ]),

                Section::make(FilamentUi::text('Points & Verification'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('points_earned')
                            ->label(FilamentUi::field('points_earned'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        DateTimePicker::make('verified_at'),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                        Toggle::make('is_featured')
                            ->label(FilamentUi::field('is_featured'))
                            ->required(),
                    ]),
            ]);
    }
}
