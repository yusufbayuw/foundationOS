<?php

namespace Modules\School\Filament\Resources\Assessments\Schemas;

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

class AssessmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('academic_period_id')
                            ->label(FilamentUi::field('academic_period_id'))
                            ->relationship('academicPeriod', 'name'),
                        Select::make('subject_id')
                            ->label(FilamentUi::field('subject_id'))
                            ->relationship('subject', 'name')
                            ->required(),
                        TextInput::make('class_id')
                            ->label(FilamentUi::field('class_id'))
                            ->required()
                            ->numeric(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code')),
                    ]),

                Section::make('Classification & Scoring')
                    ->columns(2)
                    ->schema([
                        TextInput::make('type')
                            ->label(FilamentUi::field('type')),
                        TextInput::make('assessment_category')
                            ->label(FilamentUi::field('assessment_category')),
                        TextInput::make('weight')
                            ->label(FilamentUi::field('weight'))
                            ->numeric(),
                        TextInput::make('max_score')
                            ->label(FilamentUi::field('max_score'))
                            ->numeric(),
                        TextInput::make('passing_score')
                            ->label(FilamentUi::field('passing_score'))
                            ->numeric(),
                    ]),

                Section::make('Schedule')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('schedule_date')
                            ->label(FilamentUi::field('schedule_date')),
                        DateTimePicker::make('start_time'),
                        DateTimePicker::make('end_time'),
                        TextInput::make('duration_minutes')
                            ->label(FilamentUi::field('duration_minutes'))
                            ->numeric(),
                    ]),

                Section::make('Instructions & Attachments')
                    ->columns(2)
                    ->schema([
                        Textarea::make('instructions')
                            ->label(FilamentUi::field('instructions'))
                            ->columnSpanFull(),
                        Textarea::make('attachments')
                            ->label(FilamentUi::field('attachments'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Publication & Attempts')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_published')
                            ->label(FilamentUi::field('is_published'))
                            ->required(),
                        DateTimePicker::make('published_at'),
                        Toggle::make('allow_retake')
                            ->label(FilamentUi::field('allow_retake'))
                            ->required(),
                        TextInput::make('max_attempts')
                            ->label(FilamentUi::field('max_attempts'))
                            ->numeric(),
                    ]),
            ]);
    }
}
