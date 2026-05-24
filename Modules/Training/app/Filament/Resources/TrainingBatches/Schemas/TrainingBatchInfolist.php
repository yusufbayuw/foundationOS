<?php

namespace Modules\Training\Filament\Resources\TrainingBatches\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class TrainingBatchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('tenant_id')
                        ->label(FilamentUi::field('tenant_id'))
                        ->placeholder('-'),
                    TextEntry::make('training_program_id')
                        ->label(FilamentUi::field('training_program_id'))
                        ->placeholder('-'),
                    TextEntry::make('code')
                        ->label(FilamentUi::field('code'))
                        ->placeholder('-'),
                    TextEntry::make('starts_on')
                        ->label(FilamentUi::field('starts_on'))
                        ->placeholder('-'),
                    TextEntry::make('ends_on')
                        ->label(FilamentUi::field('ends_on'))
                        ->placeholder('-'),
                    TextEntry::make('capacity')
                        ->label(FilamentUi::field('capacity'))
                        ->placeholder('-'),
                    TextEntry::make('status')
                        ->label(FilamentUi::field('status'))
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
