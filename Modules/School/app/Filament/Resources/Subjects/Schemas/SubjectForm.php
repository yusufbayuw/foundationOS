<?php

namespace Modules\School\Filament\Resources\Subjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('curriculum_id')
                            ->label(FilamentUi::field('curriculum_id'))
                            ->relationship('curriculum', 'name'),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('short_name')
                            ->label(FilamentUi::field('short_name')),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Academic Details'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('grade_level')
                            ->label(FilamentUi::field('grade_level')),
                        TextInput::make('credits')
                            ->label(FilamentUi::field('credits'))
                            ->numeric(),
                        Toggle::make('is_mandatory')
                            ->label(FilamentUi::field('is_mandatory'))
                            ->required(),
                        TextInput::make('subject_group')
                            ->label(FilamentUi::field('subject_group')),
                        Toggle::make('has_practicum')
                            ->label(FilamentUi::field('has_practicum'))
                            ->required(),
                    ]),

                Section::make(FilamentUi::text('Display & Outcomes'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('color_code')
                            ->label(FilamentUi::field('color_code')),
                        TextInput::make('icon')
                            ->label(FilamentUi::field('icon')),
                        Textarea::make('learning_outcomes')
                            ->label(FilamentUi::field('learning_outcomes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
