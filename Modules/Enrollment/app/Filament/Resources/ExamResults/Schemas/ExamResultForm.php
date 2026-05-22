<?php

namespace Modules\Enrollment\Filament\Resources\ExamResults\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class ExamResultForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Context'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('applicant_id')
                            ->label(FilamentUi::field('applicant_id'))
                            ->relationship('applicant', 'id')
                            ->required(),
                        Select::make('exam_schedule_id')
                            ->label(FilamentUi::field('exam_schedule_id'))
                            ->relationship('examSchedule', 'name'),
                        Select::make('examiner_id')
                            ->label(FilamentUi::field('examiner_id'))
                            ->relationship('examiner', 'name'),
                    ]),

                Section::make(FilamentUi::text('Exam Details'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('seat_number')
                            ->label(FilamentUi::field('seat_number')),
                        TextInput::make('score')
                            ->label(FilamentUi::field('score'))
                            ->numeric(),
                        TextInput::make('grade')
                            ->label(FilamentUi::field('grade')),
                        Toggle::make('is_passed')
                            ->label(FilamentUi::field('is_passed')),
                        Textarea::make('score_components')
                            ->label(FilamentUi::field('score_components'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Notes'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
