<?php

namespace Modules\Exam\Filament\Resources\ExamParticipants\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

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
                        TextInput::make('display_name')
                            ->label(FilamentUi::field('display_name'))
                            ->required(),
                        TextInput::make('email')
                            ->label(FilamentUi::field('email'))
                            ->email(),
                        TextInput::make('participant_code')
                            ->label(FilamentUi::field('participant_code')),
                        TextInput::make('context_reference_type')
                            ->label(FilamentUi::field('context_reference_type')),
                        TextInput::make('context_reference_id')
                            ->label(FilamentUi::field('context_reference_id'))
                            ->numeric(),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->default('registered'),
                    ]),
            ]);
    }
}
