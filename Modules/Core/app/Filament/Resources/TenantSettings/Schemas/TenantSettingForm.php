<?php

namespace Modules\Core\Filament\Resources\TenantSettings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class TenantSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Info'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        TextInput::make('group')
                            ->label(FilamentUi::field('group')),
                        TextInput::make('key')
                            ->label(FilamentUi::field('key'))
                            ->required(),
                        TextInput::make('type')
                            ->label(FilamentUi::field('type'))
                            ->required()
                            ->default('string'),
                    ]),

                Section::make(FilamentUi::text('Value'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('value')
                            ->label(FilamentUi::field('value'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
