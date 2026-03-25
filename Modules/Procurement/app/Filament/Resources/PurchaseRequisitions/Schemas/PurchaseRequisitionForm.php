<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PurchaseRequisitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('user_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('user_id'))
                    ->relationship('user', 'name'),
                TextInput::make('requested_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('requested_by'))
                    ->numeric(),
                TextInput::make('approved_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_by'))
                    ->numeric(),
                TextInput::make('request_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('request_number'))
                    ->required(),
                DatePicker::make('request_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('request_date'))
                    ->required(),
                DatePicker::make('required_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('required_date')),
                TextInput::make('priority')
                    ->label(\Modules\Core\Support\FilamentUi::field('priority'))
                    ->required()
                    ->default('normal'),
                Textarea::make('justification')
                    ->label(\Modules\Core\Support\FilamentUi::field('justification'))
                    ->columnSpanFull(),
                TextInput::make('total_items')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_items'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_estimated_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_estimated_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('draft'),
                DateTimePicker::make('approved_at'),
                Textarea::make('rejection_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('rejection_reason'))
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
