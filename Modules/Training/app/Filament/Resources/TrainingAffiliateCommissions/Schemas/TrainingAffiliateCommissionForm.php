<?php

namespace Modules\Training\Filament\Resources\TrainingAffiliateCommissions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class TrainingAffiliateCommissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TenantField::make(),
                    TextInput::make('training_affiliate_id')
                        ->label(FilamentUi::field('training_affiliate_id'))
                        ->numeric(),
                    TextInput::make('training_payment_id')
                        ->label(FilamentUi::field('training_payment_id'))
                        ->numeric(),
                    TextInput::make('amount')
                        ->label(FilamentUi::field('amount'))
                        ->numeric(),
                    TextInput::make('status')
                        ->label(FilamentUi::field('status')),
                ])
                ->columns(2),
        ]);
    }
}
