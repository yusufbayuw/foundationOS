<?php

namespace Modules\Enrollment\Filament\Resources\PromoCodes\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class PromoCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('discount_percent')
                    ->required()
                    ->numeric(),
                TextInput::make('max_uses')
                    ->numeric(),
                TextInput::make('used_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('valid_from'),
                DateTimePicker::make('valid_until'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
