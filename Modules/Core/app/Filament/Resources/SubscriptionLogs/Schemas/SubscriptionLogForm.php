<?php

namespace Modules\Core\Filament\Resources\SubscriptionLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class SubscriptionLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('action')
                    ->label(\Modules\Core\Support\FilamentUi::field('action'))
                    ->required(),
                Select::make('previous_plan_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('previous_plan_id'))
                    ->relationship('previousPlan', 'name'),
                Select::make('new_plan_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('new_plan_id'))
                    ->relationship('newPlan', 'name'),
                TextInput::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->numeric(),
                TextInput::make('currency')
                    ->label(\Modules\Core\Support\FilamentUi::field('currency')),
                TextInput::make('payment_method')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_method')),
                TextInput::make('payment_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_status')),
                TextInput::make('payment_proof')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_proof')),
                TextInput::make('invoice_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('invoice_number')),
                TextInput::make('invoice_url')
                    ->label(\Modules\Core\Support\FilamentUi::field('invoice_url'))
                    ->url(),
                DateTimePicker::make('period_start'),
                DateTimePicker::make('period_end'),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
                TextInput::make('processed_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('processed_by'))
                    ->numeric(),
                Textarea::make('metadata')
                    ->label(\Modules\Core\Support\FilamentUi::field('metadata'))
                    ->columnSpanFull(),
            ]);
    }
}
