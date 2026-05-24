<?php

namespace Modules\Training\Filament\Resources\TrainingAffiliateCommissions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class TrainingAffiliateCommissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('tenant_id')
                        ->label(FilamentUi::field('tenant_id'))
                        ->placeholder('-'),
                    TextEntry::make('training_affiliate_id')
                        ->label(FilamentUi::field('training_affiliate_id'))
                        ->placeholder('-'),
                    TextEntry::make('training_payment_id')
                        ->label(FilamentUi::field('training_payment_id'))
                        ->placeholder('-'),
                    TextEntry::make('amount')
                        ->label(FilamentUi::field('amount'))
                        ->placeholder('-'),
                    TextEntry::make('status')
                        ->label(FilamentUi::field('status'))
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
