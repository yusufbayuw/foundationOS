<?php

namespace Modules\Exam\Filament\Resources\ExamQuestionBanks\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ExamQuestionBankInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label(FilamentUi::field('name')),
                TextEntry::make('code')->label(FilamentUi::field('code')),
            ]);
    }
}
