<?php

namespace Modules\Training\Filament\Resources\TrainingBatches\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class TrainingBatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TenantField::make(),
                    TextInput::make('training_program_id')
                        ->label(FilamentUi::field('training_program_id'))
                        ->numeric(),
                    TextInput::make('code')
                        ->label(FilamentUi::field('code')),
                    TextInput::make('starts_on')
                        ->label(FilamentUi::field('starts_on')),
                    TextInput::make('ends_on')
                        ->label(FilamentUi::field('ends_on')),
                    TextInput::make('capacity')
                        ->label(FilamentUi::field('capacity'))
                        ->numeric(),
                    TextInput::make('status')
                        ->label(FilamentUi::field('status')),
                ])
                ->columns(2),
        ]);
    }
}
