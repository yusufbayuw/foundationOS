<?php

namespace Modules\Employee\Filament\Resources\Positions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class PositionForm
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
                            ->relationship('organization', 'name')
                            ->required(),
                        Select::make('department_id')
                            ->label(FilamentUi::field('department_id'))
                            ->relationship('department', 'name'),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('level')
                            ->label(FilamentUi::field('level'))
                            ->required()
                            ->numeric()
                            ->default(1),
                    ]),

                Section::make(FilamentUi::text('Details'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('job_description')
                            ->label(FilamentUi::field('job_description'))
                            ->columnSpanFull(),
                        Textarea::make('qualifications')
                            ->label(FilamentUi::field('qualifications'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Settings'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                    ]),
            ]);
    }
}
