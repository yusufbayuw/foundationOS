<?php

namespace Modules\School\Filament\Resources\Subjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('curriculum_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('curriculum_id'))
                            ->relationship('curriculum', 'name'),
                        TextInput::make('name')
                            ->label(\Modules\Core\Support\FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('short_name')
                            ->label(\Modules\Core\Support\FilamentUi::field('short_name')),
                        TextInput::make('code')
                            ->label(\Modules\Core\Support\FilamentUi::field('code'))
                            ->required(),
                        Textarea::make('description')
                            ->label(\Modules\Core\Support\FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Academic Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('grade_level')
                            ->label(\Modules\Core\Support\FilamentUi::field('grade_level')),
                        TextInput::make('credits')
                            ->label(\Modules\Core\Support\FilamentUi::field('credits'))
                            ->numeric(),
                        Toggle::make('is_mandatory')
                            ->label(\Modules\Core\Support\FilamentUi::field('is_mandatory'))
                            ->required(),
                        TextInput::make('subject_group')
                            ->label(\Modules\Core\Support\FilamentUi::field('subject_group')),
                        Toggle::make('has_practicum')
                            ->label(\Modules\Core\Support\FilamentUi::field('has_practicum'))
                            ->required(),
                    ]),

                Section::make('Display & Outcomes')
                    ->columns(2)
                    ->schema([
                        TextInput::make('color_code')
                            ->label(\Modules\Core\Support\FilamentUi::field('color_code')),
                        TextInput::make('icon')
                            ->label(\Modules\Core\Support\FilamentUi::field('icon')),
                        Textarea::make('learning_outcomes')
                            ->label(\Modules\Core\Support\FilamentUi::field('learning_outcomes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
