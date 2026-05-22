<?php

namespace Modules\School\Filament\Resources\AchievementTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class AchievementTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                    ]),

                Section::make(FilamentUi::text('Classification'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('category')
                            ->label(FilamentUi::field('category')),
                        TextInput::make('level')
                            ->label(FilamentUi::field('level')),
                        TextInput::make('point_weight')
                            ->label(FilamentUi::field('point_weight'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        TextInput::make('certificate_template')
                            ->label(FilamentUi::field('certificate_template')),
                    ]),

                Section::make(FilamentUi::text('Status'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                    ]),
            ]);
    }
}
