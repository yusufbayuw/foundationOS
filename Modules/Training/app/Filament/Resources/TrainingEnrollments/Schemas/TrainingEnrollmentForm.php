<?php

namespace Modules\Training\Filament\Resources\TrainingEnrollments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class TrainingEnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TenantField::make(),
                    TextInput::make('training_batch_id')
                        ->label(FilamentUi::field('training_batch_id'))
                        ->numeric(),
                    TextInput::make('participant_name')
                        ->label(FilamentUi::field('participant_name')),
                    TextInput::make('participant_email')
                        ->label(FilamentUi::field('participant_email')),
                    TextInput::make('status')
                        ->label(FilamentUi::field('status')),
                ])
                ->columns(2),
        ]);
    }
}
