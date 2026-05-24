<?php

namespace Modules\School\Filament\Resources\StudentAbsences\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StudentAbsenceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('tenant_id')
                        ->label(FilamentUi::field('tenant_id'))
                        ->placeholder('-'),
                    TextEntry::make('student_id')
                        ->label(FilamentUi::field('student_id'))
                        ->placeholder('-'),
                    TextEntry::make('requested_by_user_id')
                        ->label(FilamentUi::field('requested_by_user_id'))
                        ->placeholder('-'),
                    TextEntry::make('absence_type')
                        ->label(FilamentUi::field('absence_type'))
                        ->placeholder('-'),
                    TextEntry::make('start_date')
                        ->label(FilamentUi::field('start_date'))
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('end_date')
                        ->label(FilamentUi::field('end_date'))
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('reason')
                        ->label(FilamentUi::field('reason'))
                        ->placeholder('-'),
                    TextEntry::make('status')
                        ->label(FilamentUi::field('status'))
                        ->placeholder('-'),
                    TextEntry::make('file_upload_id')
                        ->label(FilamentUi::field('file_upload_id'))
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
