<?php

namespace Modules\School\Filament\Resources\ExtracurricularEnrollments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ExtracurricularEnrollmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('tenant_id')
                        ->label(FilamentUi::field('tenant_id'))
                        ->placeholder('-'),
                    TextEntry::make('extracurricular_id')
                        ->label(FilamentUi::field('extracurricular_id'))
                        ->placeholder('-'),
                    TextEntry::make('student_id')
                        ->label(FilamentUi::field('student_id'))
                        ->placeholder('-'),
                    TextEntry::make('status')
                        ->label(FilamentUi::field('status'))
                        ->placeholder('-'),
                    TextEntry::make('enrolled_at')
                        ->label(FilamentUi::field('enrolled_at'))
                        ->dateTime()
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
