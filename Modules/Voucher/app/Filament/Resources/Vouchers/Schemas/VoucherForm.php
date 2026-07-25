<?php

namespace Modules\Voucher\Filament\Resources\Vouchers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class VoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('Voucher Details'))
                ->columns(2)
                ->schema([
                    TenantField::make(),
                    TextInput::make('title')
                        ->label(FilamentUi::field('title'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('code')
                        ->label(FilamentUi::field('code'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('quota')
                        ->label(FilamentUi::field('quota'))
                        ->required()
                        ->numeric()
                        ->minValue(1),
                    Select::make('status')
                        ->label(FilamentUi::field('status'))
                        ->options([
                            'draft' => 'Draft',
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                            'expired' => 'Expired',
                        ])
                        ->required()
                        ->default('draft'),
                    Select::make('redemption_method')
                        ->label(FilamentUi::field('redemption_method'))
                        ->options([
                            'manual' => 'Manual',
                            'qr' => 'QR Code',
                            'pos' => 'Point of Sale',
                        ])
                        ->required()
                        ->default('manual'),
                    DateTimePicker::make('start_at')
                        ->label(FilamentUi::field('start_at')),
                    DateTimePicker::make('end_at')
                        ->label(FilamentUi::field('end_at')),
                    Textarea::make('description')
                        ->label(FilamentUi::field('description'))
                        ->columnSpanFull(),
                    Textarea::make('terms')
                        ->label(FilamentUi::field('terms'))
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
