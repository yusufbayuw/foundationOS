<?php

namespace Modules\Exam\Filament\Resources\ExamParticipants\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Enums\ParticipantStatus;

class ExamParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('exam_definition_id')
                            ->label(FilamentUi::field('exam_definition_id'))
                            ->relationship('examDefinition', 'name')
                            ->required()
                            ->searchable(),
                        Select::make('participant_source')
                            ->label(FilamentUi::field('participant_source'))
                            ->options(collect(ParticipantSource::cases())->mapWithKeys(
                                fn (ParticipantSource $case) => [$case->value => FilamentUi::text($case->label())]
                            ))
                            ->required(),
                        TextInput::make('student_name')
                            ->label(FilamentUi::field('student_name'))
                            ->required(),
                        TextInput::make('email')
                            ->label(FilamentUi::field('email'))
                            ->email(),
                        TextInput::make('student_identifier')
                            ->label(FilamentUi::field('student_identifier')),
                        Select::make('status')
                            ->label(FilamentUi::field('status'))
                            ->options(collect(ParticipantStatus::cases())->mapWithKeys(
                                fn (ParticipantStatus $case) => [$case->value => FilamentUi::text($case->label())]
                            ))
                            ->default(ParticipantStatus::Assigned)
                            ->required(),
                        DateTimePicker::make('assigned_at')
                            ->label(FilamentUi::field('assigned_at')),
                        TextInput::make('context_reference_type')
                            ->label(FilamentUi::field('context_reference_type')),
                        TextInput::make('participant_legacy_id')
                            ->label(FilamentUi::field('participant_legacy_id'))
                            ->numeric(),
                    ]),
            ]);
    }
}
