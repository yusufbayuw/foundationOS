<?php

namespace Modules\Enrollment\Filament\Resources\Registrations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class RegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('applicant_id')
                            ->label(FilamentUi::field('applicant_id'))
                            ->relationship('applicant', 'id')
                            ->required(),
                        DatePicker::make('registration_date')
                            ->label(FilamentUi::field('registration_date'))
                            ->required(),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('pending'),
                        TextInput::make('completed_by')
                            ->label(FilamentUi::field('completed_by'))
                            ->numeric(),
                        DateTimePicker::make('completed_at'),
                    ]),

                Section::make(FilamentUi::text('Payment'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('payment_status')
                            ->label(FilamentUi::field('payment_status'))
                            ->required()
                            ->default('unpaid'),
                        TextInput::make('total_fee')
                            ->label(FilamentUi::field('total_fee'))
                            ->required()
                            ->numeric(),
                        TextInput::make('paid_amount')
                            ->label(FilamentUi::field('paid_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('uniform_size')
                            ->label(FilamentUi::field('uniform_size')),
                    ]),

                Section::make(FilamentUi::text('Documents & Notes'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('documents_received')
                            ->label(FilamentUi::field('documents_received'))
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
