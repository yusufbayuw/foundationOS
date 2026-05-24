<?php

namespace Modules\School\Filament\Resources\StudentAbsences\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class StudentAbsenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TenantField::make(),
                    TextInput::make('student_id')
                        ->label(FilamentUi::field('student_id'))
                        ->numeric(),
                    TextInput::make('requested_by_user_id')
                        ->label(FilamentUi::field('requested_by_user_id'))
                        ->numeric(),
                    TextInput::make('absence_type')
                        ->label(FilamentUi::field('absence_type')),
                    TextInput::make('start_date')
                        ->label(FilamentUi::field('start_date')),
                    TextInput::make('end_date')
                        ->label(FilamentUi::field('end_date')),
                    TextInput::make('reason')
                        ->label(FilamentUi::field('reason')),
                    TextInput::make('status')
                        ->label(FilamentUi::field('status')),
                    TextInput::make('file_upload_id')
                        ->label(FilamentUi::field('file_upload_id'))
                        ->numeric(),
                ])
                ->columns(2),
        ]);
    }
}
