<?php

namespace Modules\Enrollment\Filament\Resources\Registrations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class RegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('applicant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('applicant_id'))
                    ->relationship('applicant', 'id')
                    ->required(),
                TextInput::make('completed_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('completed_by'))
                    ->numeric(),
                DatePicker::make('registration_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('registration_date'))
                    ->required(),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('pending'),
                TextInput::make('payment_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_status'))
                    ->required()
                    ->default('unpaid'),
                TextInput::make('total_fee')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_fee'))
                    ->required()
                    ->numeric(),
                TextInput::make('paid_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('paid_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('uniform_size')
                    ->label(\Modules\Core\Support\FilamentUi::field('uniform_size')),
                Textarea::make('documents_received')
                    ->label(\Modules\Core\Support\FilamentUi::field('documents_received'))
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
