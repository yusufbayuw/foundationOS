<?php

namespace Modules\Campus\Filament\Resources\StudyPlanItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class StudyPlanItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('study_plan_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('study_plan_id'))
                    ->relationship('studyPlan', 'id')
                    ->required(),
                Select::make('course_offering_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('course_offering_id'))
                    ->relationship('courseOffering', 'id'),
                Select::make('course_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('course_id'))
                    ->relationship('course', 'name'),
                TextInput::make('credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('credits'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('enrolled'),
                TextInput::make('grade_letter')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_letter')),
                TextInput::make('grade_point')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_point'))
                    ->numeric(),
                Textarea::make('remarks')
                    ->label(\Modules\Core\Support\FilamentUi::field('remarks'))
                    ->columnSpanFull(),
            ]);
    }
}
