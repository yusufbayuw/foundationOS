<?php

namespace Modules\Campus\Filament\Resources\StudyPlans\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StudyPlanForm
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
                Select::make('academic_period_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('academic_period_id'))
                    ->relationship('academicPeriod', 'name'),
                TextInput::make('approved_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_by'))
                    ->numeric(),
                TextInput::make('plan_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('plan_number')),
                TextInput::make('total_credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_credits'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('draft'),
                DateTimePicker::make('submitted_at'),
                DateTimePicker::make('approved_at'),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
