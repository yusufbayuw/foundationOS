<?php

namespace Modules\Employee\Filament\Resources\KpiTemplates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class KpiTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Template Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name')
                            ->required(),
                        Select::make('department_id')
                            ->label(FilamentUi::field('department_id'))
                            ->relationship('department', 'name'),
                        Select::make('position_id')
                            ->label(FilamentUi::field('position_id'))
                            ->relationship('position', 'name'),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                    ]),

                Section::make(FilamentUi::text('Indicators & Weight'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('indicators')
                            ->label(FilamentUi::field('indicators'))
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('total_weight')
                            ->label(FilamentUi::field('total_weight'))
                            ->required()
                            ->numeric()
                            ->default(100),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                    ]),
            ]);
    }
}
