<?php

namespace Modules\Campus\Filament\Resources\StudyPlans\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class StudyPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Relationships'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('collage_student_id')
                            ->label(FilamentUi::field('collage_student_id'))
                            ->relationship('collageStudent', 'id')
                            ->required(),
                        Select::make('academic_period_id')
                            ->label(FilamentUi::field('academic_period_id'))
                            ->relationship('academicPeriod', 'name'),
                    ]),

                Section::make(FilamentUi::text('Plan Details'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('plan_number')
                            ->label(FilamentUi::field('plan_number')),
                        TextInput::make('total_credits')
                            ->label(FilamentUi::field('total_credits'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('draft'),
                        TextInput::make('approved_by')
                            ->label(FilamentUi::field('approved_by'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Approval'))
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('submitted_at'),
                        DateTimePicker::make('approved_at'),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
