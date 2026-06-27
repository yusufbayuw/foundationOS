<?php

namespace Modules\Finance\Filament\Resources\CustomerInvoices\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\CustomerInvoiceItem;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('description')
                ->label(FilamentUi::field('description'))
                ->required()
                ->columnSpanFull(),

            TextInput::make('quantity')
                ->label(FilamentUi::field('quantity'))
                ->numeric()
                ->default(1)
                ->required(),

            TextInput::make('unit')
                ->label(FilamentUi::field('unit'))
                ->placeholder('pcs / jam / bulan'),

            TextInput::make('unit_price')
                ->label(FilamentUi::field('unit_price'))
                ->numeric()
                ->prefix('Rp')
                ->required()
                ->default(0),

            TextInput::make('discount_amount')
                ->label(FilamentUi::field('discount_amount'))
                ->numeric()
                ->prefix('Rp')
                ->default(0),

            Select::make('chart_of_account_id')
                ->label(FilamentUi::field('chart_of_account_id'))
                ->options(fn () => ChartOfAccount::withoutTenantScope()
                    ->where('tenant_id', filament()->getTenant()?->getKey())
                    ->where('is_active', true)
                    ->whereIn('type', ['revenue', 'asset'])
                    ->orderBy('code')
                    ->get()
                    ->mapWithKeys(fn ($coa) => [$coa->id => "{$coa->code} — {$coa->name}"])
                    ->toArray()
                )
                ->searchable()
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label(FilamentUi::field('description'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(FilamentUi::field('quantity'))
                    ->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('unit')
                    ->label(FilamentUi::field('unit'))
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label(FilamentUi::field('unit_price'))
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('line_total')
                    ->label(FilamentUi::field('line_total'))
                    ->money('IDR')
                    ->weight('bold'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['tenant_id'] = filament()->getTenant()?->getKey();

                        return $data;
                    })
                    ->after(fn (CustomerInvoiceItem $record) => $record->customerInvoice->recalculate()),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(fn (CustomerInvoiceItem $record) => $record->customerInvoice->recalculate()),
                DeleteAction::make()
                    ->after(fn (CustomerInvoiceItem $record) => $record->customerInvoice->recalculate()),
            ])
            ->defaultSort('sort_order');
    }
}
