<?php

namespace Modules\School\Filament\Resources\Assessments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class AssessmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name'),
                Select::make('academic_period_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('academic_period_id'))
                    ->relationship('academicPeriod', 'name'),
                Select::make('subject_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('subject_id'))
                    ->relationship('subject', 'name')
                    ->required(),
                TextInput::make('class_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('class_id'))
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextInput::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type')),
                TextInput::make('assessment_category')
                    ->label(\Modules\Core\Support\FilamentUi::field('assessment_category')),
                TextInput::make('weight')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight'))
                    ->numeric(),
                TextInput::make('max_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_score'))
                    ->numeric(),
                TextInput::make('passing_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('passing_score'))
                    ->numeric(),
                DatePicker::make('schedule_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('schedule_date')),
                DateTimePicker::make('start_time'),
                DateTimePicker::make('end_time'),
                TextInput::make('duration_minutes')
                    ->label(\Modules\Core\Support\FilamentUi::field('duration_minutes'))
                    ->numeric(),
                Textarea::make('instructions')
                    ->label(\Modules\Core\Support\FilamentUi::field('instructions'))
                    ->columnSpanFull(),
                Textarea::make('attachments')
                    ->label(\Modules\Core\Support\FilamentUi::field('attachments'))
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_published'))
                    ->required(),
                DateTimePicker::make('published_at'),
                Toggle::make('allow_retake')
                    ->label(\Modules\Core\Support\FilamentUi::field('allow_retake'))
                    ->required(),
                TextInput::make('max_attempts')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_attempts'))
                    ->numeric(),
            ]);
    }
}
