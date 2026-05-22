<?php

namespace Modules\School\Filament\Resources\ViolationTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class ViolationTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
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
                        TextInput::make('severity_level')
                            ->label(FilamentUi::field('severity_level')),
                        TextInput::make('point_weight')
                            ->label(FilamentUi::field('point_weight'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                    ]),

                Section::make(FilamentUi::text('Details & Actions'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('default_sanctions')
                            ->label(FilamentUi::field('default_sanctions'))
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                        Textarea::make('prevention_measures')
                            ->label(FilamentUi::field('prevention_measures'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
