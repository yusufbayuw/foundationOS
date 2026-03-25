<?php

namespace Modules\School\Filament\Resources\ViolationTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ViolationTypeForm
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
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->required(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('category')
                    ->label(\Modules\Core\Support\FilamentUi::field('category')),
                TextInput::make('severity_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('severity_level')),
                Textarea::make('default_sanctions')
                    ->label(\Modules\Core\Support\FilamentUi::field('default_sanctions'))
                    ->columnSpanFull(),
                TextInput::make('point_weight')
                    ->label(\Modules\Core\Support\FilamentUi::field('point_weight'))
                    ->required()
                    ->numeric()
                    ->default(1),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                Textarea::make('prevention_measures')
                    ->label(\Modules\Core\Support\FilamentUi::field('prevention_measures'))
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
            ]);
    }
}
