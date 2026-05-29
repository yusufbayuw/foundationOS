<?php

namespace Modules\Exam\Filament\Resources\ExamTokens\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ExamTokenInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('token')->label(FilamentUi::field('token')),
                TextEntry::make('examParticipant.student_name')
                    ->label(FilamentUi::field('exam_participant_id')),
                IconEntry::make('is_active')->label(FilamentUi::field('is_active'))->boolean(),
            ]);
    }
}
