<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ExamDefinitionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label(FilamentUi::field('name')),
                TextEntry::make('code')
                    ->label(FilamentUi::field('code')),
                TextEntry::make('exam_academic_context')
                    ->label(FilamentUi::field('exam_academic_context')),
                TextEntry::make('status')
                    ->label(FilamentUi::field('status')),
                TextEntry::make('runtime_exam_id')
                    ->label(FilamentUi::field('runtime_exam_id')),
            ]);
    }
}
