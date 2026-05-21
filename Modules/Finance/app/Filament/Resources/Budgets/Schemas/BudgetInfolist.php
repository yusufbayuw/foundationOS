<?php

namespace Modules\Finance\Filament\Resources\Budgets\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class BudgetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Budget Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization')),
                        TextEntry::make('chartOfAccount.name')
                            ->label(FilamentUi::text('Chart of account')),
                        TextEntry::make('fiscal_year')
                            ->label(FilamentUi::field('fiscal_year')),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code')),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('workflowInstances_count')
                            ->label('Workflow Instances')
                            ->state(fn ($record): int => $record->workflowInstances()->count()),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Amounts')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('allocated_amount')
                            ->label(FilamentUi::field('allocated_amount'))
                            ->numeric(),
                        TextEntry::make('used_amount')
                            ->label(FilamentUi::field('used_amount'))
                            ->numeric(),
                        TextEntry::make('remaining_amount')
                            ->label(FilamentUi::field('remaining_amount'))
                            ->numeric(),
                    ]),

                Section::make('Approval')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('approved_by')
                            ->label(FilamentUi::field('approved_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('approved_at')
                            ->label(FilamentUi::field('approved_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
