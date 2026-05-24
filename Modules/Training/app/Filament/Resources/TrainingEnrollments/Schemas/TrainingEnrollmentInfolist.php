<?php

namespace Modules\Training\Filament\Resources\TrainingEnrollments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class TrainingEnrollmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('tenant_id')
                        ->label(FilamentUi::field('tenant_id'))
                        ->placeholder('-'),
                    TextEntry::make('training_batch_id')
                        ->label(FilamentUi::field('training_batch_id'))
                        ->placeholder('-'),
                    TextEntry::make('participant_name')
                        ->label(FilamentUi::field('participant_name'))
                        ->placeholder('-'),
                    TextEntry::make('participant_email')
                        ->label(FilamentUi::field('participant_email'))
                        ->placeholder('-'),
                    TextEntry::make('status')
                        ->label(FilamentUi::field('status'))
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
