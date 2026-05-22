<?php

namespace Modules\Campus\Filament\Resources\StudyPrograms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class StudyProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Program Details'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('faculty_id')
                            ->label(FilamentUi::field('faculty_id'))
                            ->relationship('faculty', 'name'),
                        Select::make('head_of_program_id')
                            ->label(FilamentUi::field('head_of_program_id'))
                            ->relationship('headOfProgram', 'id'),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code')),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('degree_level')
                            ->label(FilamentUi::field('degree_level')),
                        TextInput::make('accreditation')
                            ->label(FilamentUi::field('accreditation')),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Curriculum & Status'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('total_credits_required')
                            ->label(FilamentUi::field('total_credits_required'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                    ]),
            ]);
    }
}
