<?php

namespace Modules\Exam\Filament\Resources\ExamTokens\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class ExamTokenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('exam_participant_id')
                            ->label(FilamentUi::field('exam_participant_id'))
                            ->relationship('examParticipant', 'student_name')
                            ->required()
                            ->searchable(),
                        TextInput::make('token')
                            ->label(FilamentUi::field('token'))
                            ->required(),
                        DateTimePicker::make('expires_at')
                            ->label(FilamentUi::field('expires_at')),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->default(true),
                    ]),
            ]);
    }
}
