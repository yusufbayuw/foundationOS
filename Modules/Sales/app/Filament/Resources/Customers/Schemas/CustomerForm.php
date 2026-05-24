<?php

namespace Modules\Sales\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TenantField::make(),
                    TextInput::make('organization_id')
                        ->label(FilamentUi::field('organization_id'))
                        ->numeric(),
                    TextInput::make('code')
                        ->label(FilamentUi::field('code')),
                    TextInput::make('name')
                        ->label(FilamentUi::field('name')),
                    TextInput::make('email')
                        ->label(FilamentUi::field('email')),
                    TextInput::make('phone')
                        ->label(FilamentUi::field('phone')),
                    Textarea::make('address')
                        ->label(FilamentUi::field('address'))
                        ->columnSpanFull(),
                    TextInput::make('is_active')
                        ->label(FilamentUi::field('is_active')),
                    TextInput::make('is_cooperative_member')
                        ->label(FilamentUi::field('is_cooperative_member')),
                    TextInput::make('member_number')
                        ->label(FilamentUi::field('member_number')),
                    TextInput::make('member_discount_percent')
                        ->label(FilamentUi::field('member_discount_percent'))
                        ->numeric(),
                ])
                ->columns(2),
        ]);
    }
}
