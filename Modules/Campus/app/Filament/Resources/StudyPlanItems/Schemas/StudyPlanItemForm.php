<?php

namespace Modules\Campus\Filament\Resources\StudyPlanItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class StudyPlanItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Relationships')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('study_plan_id')
                            ->label(FilamentUi::field('study_plan_id'))
                            ->relationship('studyPlan', 'id')
                            ->required(),
                        Select::make('course_offering_id')
                            ->label(FilamentUi::field('course_offering_id'))
                            ->relationship('courseOffering', 'id'),
                        Select::make('course_id')
                            ->label(FilamentUi::field('course_id'))
                            ->relationship('course', 'name'),
                    ]),

                Section::make('Enrollment Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('credits')
                            ->label(FilamentUi::field('credits'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('enrolled'),
                    ]),

                Section::make('Grade')
                    ->columns(2)
                    ->schema([
                        TextInput::make('grade_letter')
                            ->label(FilamentUi::field('grade_letter')),
                        TextInput::make('grade_point')
                            ->label(FilamentUi::field('grade_point'))
                            ->numeric(),
                        Textarea::make('remarks')
                            ->label(FilamentUi::field('remarks'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
