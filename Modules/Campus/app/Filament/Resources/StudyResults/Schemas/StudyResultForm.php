<?php

namespace Modules\Campus\Filament\Resources\StudyResults\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class StudyResultForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Relationships')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('study_plan_item_id')
                            ->label(FilamentUi::field('study_plan_item_id'))
                            ->relationship('studyPlanItem', 'id'),
                    ]),

                Section::make('Grade')
                    ->columns(2)
                    ->schema([
                        TextInput::make('grade_letter')
                            ->label(FilamentUi::field('grade_letter')),
                        TextInput::make('grade_point')
                            ->label(FilamentUi::field('grade_point'))
                            ->numeric(),
                        TextInput::make('weight_score')
                            ->label(FilamentUi::field('weight_score'))
                            ->numeric(),
                        Toggle::make('passed')
                            ->label(FilamentUi::field('passed'))
                            ->required(),
                        DateTimePicker::make('published_at'),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
