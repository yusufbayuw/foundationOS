<?php

namespace Modules\Employee\Filament\Resources\Positions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class PositionForm
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
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->required(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('level')
                    ->label(\Modules\Core\Support\FilamentUi::field('level'))
                    ->required()
                    ->numeric()
                    ->default(1),
                Textarea::make('job_description')
                    ->label(\Modules\Core\Support\FilamentUi::field('job_description'))
                    ->columnSpanFull(),
                Textarea::make('qualifications')
                    ->label(\Modules\Core\Support\FilamentUi::field('qualifications'))
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
            ]);
    }
}
