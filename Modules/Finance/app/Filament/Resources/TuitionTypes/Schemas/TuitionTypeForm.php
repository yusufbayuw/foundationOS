<?php

namespace Modules\Finance\Filament\Resources\TuitionTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class TuitionTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name')
                            ->required(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code')),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('education_level')
                            ->label(FilamentUi::field('education_level')),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Billing')
                    ->columns(2)
                    ->schema([
                        TextInput::make('amount')
                            ->label(FilamentUi::field('amount'))
                            ->numeric(),
                        TextInput::make('frequency')
                            ->label(FilamentUi::field('frequency')),
                        TextInput::make('due_day')
                            ->label(FilamentUi::field('due_day'))
                            ->required()
                            ->numeric()
                            ->default(10),
                        TextInput::make('grace_period_days')
                            ->label(FilamentUi::field('grace_period_days'))
                            ->required()
                            ->numeric()
                            ->default(7),
                    ]),

                Section::make('Late Fees & Discounts')
                    ->columns(2)
                    ->schema([
                        TextInput::make('late_fee_percentage')
                            ->label(FilamentUi::field('late_fee_percentage'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('late_fee_fixed')
                            ->label(FilamentUi::field('late_fee_fixed'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        Toggle::make('discount_eligible')
                            ->label(FilamentUi::field('discount_eligible'))
                            ->required(),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                    ]),
            ]);
    }
}
