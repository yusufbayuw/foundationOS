<?php

namespace Modules\School\Filament\Resources\ExtracurricularEnrollments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class ExtracurricularEnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TenantField::make(),
                    TextInput::make('extracurricular_id')
                        ->label(FilamentUi::field('extracurricular_id'))
                        ->numeric(),
                    TextInput::make('student_id')
                        ->label(FilamentUi::field('student_id'))
                        ->numeric(),
                    TextInput::make('status')
                        ->label(FilamentUi::field('status')),
                    TextInput::make('enrolled_at')
                        ->label(FilamentUi::field('enrolled_at')),
                ])
                ->columns(2),
        ]);
    }
}
