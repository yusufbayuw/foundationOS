<?php

namespace Modules\Employee\Filament\Resources\KpiTemplates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class KpiTemplateForm
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
                    ->relationship('organization', 'name')
                    ->required(),
                Select::make('department_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('department_id'))
                    ->relationship('department', 'name'),
                Select::make('position_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('position_id'))
                    ->relationship('position', 'name'),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                Textarea::make('indicators')
                    ->label(\Modules\Core\Support\FilamentUi::field('indicators'))
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('total_weight')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_weight'))
                    ->required()
                    ->numeric()
                    ->default(100),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
            ]);
    }
}
