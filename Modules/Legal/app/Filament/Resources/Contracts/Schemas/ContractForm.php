<?php

namespace Modules\Legal\Filament\Resources\Contracts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))->schema([
                TextInput::make('code')->label(FilamentUi::field('code')),
                TextInput::make('name')->label(FilamentUi::field('name'))->required(),
                Select::make('status')->label(FilamentUi::field('status'))->options([
                    'active' => FilamentUi::text('Active'),
                    'inactive' => FilamentUi::text('Inactive'),
                ])->default('active'),
                Textarea::make('description')->label(FilamentUi::field('description'))->columnSpanFull(),
                DatePicker::make('effective_date')->label(FilamentUi::field('effective_date')),
                DatePicker::make('expires_at')->label(FilamentUi::field('expires_at')),
                Toggle::make('auto_renew')->label(FilamentUi::field('auto_renew')),
                TextInput::make('notice_period_days')->label(FilamentUi::field('notice_period_days'))->numeric()->default(30),
            ])->columns(2),
        ]);
    }
}
