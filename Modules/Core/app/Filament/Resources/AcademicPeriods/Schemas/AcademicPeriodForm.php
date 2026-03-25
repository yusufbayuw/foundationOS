<?php

namespace Modules\Core\Filament\Resources\AcademicPeriods\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademicPeriodForm
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
                Select::make('academic_year_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('academic_year_id'))
                    ->relationship('academicYear', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->required(),
                TextInput::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type')),
                DatePicker::make('start_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_date'))
                    ->required(),
                DatePicker::make('end_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_date'))
                    ->required(),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
                Toggle::make('is_locked')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_locked'))
                    ->required(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
            ]);
    }
}
