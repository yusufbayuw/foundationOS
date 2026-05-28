<?php

namespace Modules\Exam\Filament\Resources\ExamParticipants\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ExamParticipantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('display_name')->label(FilamentUi::field('display_name')),
                TextEntry::make('examDefinition.name')->label(FilamentUi::field('exam_definition_id')),
                TextEntry::make('status')->label(FilamentUi::field('status')),
            ]);
    }
}
