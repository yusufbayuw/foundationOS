<?php

namespace Modules\Training\Filament\Resources\TrainingCertificates\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class TrainingCertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TenantField::make(),
                    TextInput::make('training_enrollment_id')
                        ->label(FilamentUi::field('training_enrollment_id'))
                        ->numeric(),
                    TextInput::make('certificate_number')
                        ->label(FilamentUi::field('certificate_number')),
                    TextInput::make('verification_token')
                        ->label(FilamentUi::field('verification_token')),
                    TextInput::make('issued_at')
                        ->label(FilamentUi::field('issued_at')),
                ])
                ->columns(2),
        ]);
    }
}
