<?php

namespace Modules\Campus\Filament\Resources\Theses\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class ThesisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('collage_student_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('collage_student_id'))
                    ->relationship('collageStudent', 'id')
                    ->required(),
                Select::make('advisor_lecturer_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('advisor_lecturer_id'))
                    ->relationship('advisorLecturer', 'id'),
                Select::make('examiner_lecturer_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('examiner_lecturer_id'))
                    ->relationship('examinerLecturer', 'id'),
                TextInput::make('title')
                    ->label(\Modules\Core\Support\FilamentUi::field('title'))
                    ->required(),
                TextInput::make('research_area')
                    ->label(\Modules\Core\Support\FilamentUi::field('research_area')),
                DateTimePicker::make('proposal_submitted_at'),
                DateTimePicker::make('defense_date'),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('proposal'),
                TextInput::make('grade_letter')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_letter')),
                TextInput::make('grade_point')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_point'))
                    ->numeric(),
                TextInput::make('document_path')
                    ->label(\Modules\Core\Support\FilamentUi::field('document_path')),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
