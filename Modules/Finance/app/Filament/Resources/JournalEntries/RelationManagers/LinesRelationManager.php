<?php

namespace Modules\Finance\Filament\Resources\JournalEntries\RelationManagers;

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

class LinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('chart_of_account_id')
                ->label(FilamentUi::field('chart_of_account_id'))
                ->options(fn () => ChartOfAccount::withoutTenantScope()
                    ->where('tenant_id', filament()->getTenant()?->getKey())
                    ->where('is_active', true)
                    ->orderBy('code')
                    ->get()
                    ->mapWithKeys(fn ($coa) => [$coa->id => "{$coa->code} — {$coa->name}"])
                    ->toArray()
                )
                ->searchable()
                ->required(),

            TextInput::make('description')
                ->label(FilamentUi::field('description'))
                ->columnSpanFull(),

            TextInput::make('debit')
                ->label(FilamentUi::field('debit'))
                ->numeric()
                ->prefix('Rp')
                ->default(0),

            TextInput::make('credit')
                ->label(FilamentUi::field('credit'))
                ->numeric()
                ->prefix('Rp')
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('chartOfAccount.code')
                    ->label(FilamentUi::field('chart_of_account_id'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(FilamentUi::field('description'))
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('debit')
                    ->label(FilamentUi::field('debit'))
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('credit')
                    ->label(FilamentUi::field('credit'))
                    ->money('IDR'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['tenant_id'] = filament()->getTenant()?->getKey();

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public function isReadOnly(): bool
    {
        return $this->getOwnerRecord()->is_posted ?? false;
    }
}
