<?php

namespace Modules\Library\Filament\Resources\Members\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MemberForm
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
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('member_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('member_number'))
                    ->required(),
                TextInput::make('member_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('member_type')),
                DatePicker::make('joined_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('joined_at')),
                DatePicker::make('expires_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('expires_at')),
                TextInput::make('max_books')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_books'))
                    ->required()
                    ->numeric()
                    ->default(3),
                TextInput::make('loan_period_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('loan_period_days'))
                    ->required()
                    ->numeric()
                    ->default(7),
                TextInput::make('fine_per_day')
                    ->label(\Modules\Core\Support\FilamentUi::field('fine_per_day'))
                    ->required()
                    ->numeric()
                    ->default(1000),
                TextInput::make('total_loans_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_loans_count'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('current_loans_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('current_loans_count'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_fines')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_fines'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('unpaid_fines')
                    ->label(\Modules\Core\Support\FilamentUi::field('unpaid_fines'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('active'),
                Textarea::make('suspension_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('suspension_reason'))
                    ->columnSpanFull(),
                DatePicker::make('suspension_until')
                    ->label(\Modules\Core\Support\FilamentUi::field('suspension_until')),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
