<?php

namespace Modules\Training\Filament\Resources\TrainingCertificates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class TrainingCertificateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('tenant_id')
                        ->label(FilamentUi::field('tenant_id'))
                        ->placeholder('-'),
                    TextEntry::make('training_enrollment_id')
                        ->label(FilamentUi::field('training_enrollment_id'))
                        ->placeholder('-'),
                    TextEntry::make('certificate_number')
                        ->label(FilamentUi::field('certificate_number'))
                        ->placeholder('-'),
                    TextEntry::make('verification_token')
                        ->label(FilamentUi::field('verification_token'))
                        ->placeholder('-'),
                    TextEntry::make('issued_at')
                        ->label(FilamentUi::field('issued_at'))
                        ->dateTime()
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
