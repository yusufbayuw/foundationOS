<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiActuals\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KpiActualForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('organization_id')
                    ->relationship('organization', 'name'),
                TextInput::make('code'),
                TextInput::make('name'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('meta')
                    ->columnSpanFull(),
                TextInput::make('kpi_metric_id')
                    ->numeric(),
                TextInput::make('period'),
                TextInput::make('actual_value')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('score')
                    ->numeric(),
            ]);
    }
}
