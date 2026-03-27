<?php

namespace Modules\Campus\Filament\Resources\StudyResults\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class StudyResultForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('study_plan_item_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('study_plan_item_id'))
                    ->relationship('studyPlanItem', 'id'),
                TextInput::make('grade_letter')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_letter')),
                TextInput::make('grade_point')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_point'))
                    ->numeric(),
                TextInput::make('weight_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight_score'))
                    ->numeric(),
                Toggle::make('passed')
                    ->label(\Modules\Core\Support\FilamentUi::field('passed'))
                    ->required(),
                DateTimePicker::make('published_at'),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
