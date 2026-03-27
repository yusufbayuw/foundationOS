<?php

namespace Modules\Enrollment\Filament\Resources\ExamResults\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class ExamResultForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('applicant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('applicant_id'))
                    ->relationship('applicant', 'id')
                    ->required(),
                Select::make('examiner_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('examiner_id'))
                    ->relationship('examiner', 'name'),
                TextInput::make('seat_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('seat_number')),
                TextInput::make('score')
                    ->label(\Modules\Core\Support\FilamentUi::field('score'))
                    ->numeric(),
                Textarea::make('score_components')
                    ->label(\Modules\Core\Support\FilamentUi::field('score_components'))
                    ->columnSpanFull(),
                TextInput::make('grade')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade')),
                Toggle::make('is_passed')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_passed')),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
                Select::make('exam_schedule_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('exam_schedule_id'))
                    ->relationship('examSchedule', 'name'),
            ]);
    }
}
