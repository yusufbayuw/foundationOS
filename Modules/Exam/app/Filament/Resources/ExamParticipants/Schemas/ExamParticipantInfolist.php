<?php

namespace Modules\Exam\Filament\Resources\ExamParticipants\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Enums\ParticipantStatus;

class ExamParticipantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('student_name')
                    ->label(FilamentUi::field('student_name')),
                TextEntry::make('examDefinition.name')
                    ->label(FilamentUi::field('exam_definition_id')),
                TextEntry::make('student_identifier')
                    ->label(FilamentUi::field('student_identifier')),
                TextEntry::make('participant_source')
                    ->label(FilamentUi::field('participant_source'))
                    ->formatStateUsing(fn (?ParticipantSource $state): string => $state !== null
                        ? FilamentUi::text($state->label())
                        : '-'),
                TextEntry::make('status')
                    ->label(FilamentUi::field('status'))
                    ->formatStateUsing(fn (?ParticipantStatus $state): string => $state !== null
                        ? FilamentUi::text($state->label())
                        : '-'),
                TextEntry::make('activeToken.token')
                    ->label(FilamentUi::field('token')),
            ]);
    }
}
