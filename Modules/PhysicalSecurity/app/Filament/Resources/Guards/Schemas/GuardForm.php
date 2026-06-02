<?php

namespace Modules\PhysicalSecurity\Filament\Resources\Guards\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class GuardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General information'))
                    ->schema([
                        TenantField::make(),
                        TenantField::organizationSelect(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        Select::make('status')
                            ->label(FilamentUi::field('status'))
                            ->options([
                                'active' => FilamentUi::text('Active'),
                                'inactive' => FilamentUi::text('Inactive'),
                            ])
                            ->default('active'),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                        KeyValue::make('meta')
                            ->label(FilamentUi::field('meta'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
