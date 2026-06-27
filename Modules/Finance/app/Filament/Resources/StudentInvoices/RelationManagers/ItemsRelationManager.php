<?php

namespace Modules\Finance\Filament\Resources\StudentInvoices\RelationManagers;

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
use Modules\Finance\Models\TuitionType;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('tuition_type_id')
                ->label(FilamentUi::field('tuition_type_id'))
                ->options(fn () => TuitionType::withoutTenantScope()
                    ->where('tenant_id', filament()->getTenant()?->id)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray()
                )
                ->searchable()
                ->nullable(),

            TextInput::make('description')
                ->label(FilamentUi::field('description'))
                ->required()
                ->columnSpanFull(),

            TextInput::make('quantity')
                ->label(FilamentUi::field('quantity'))
                ->numeric()
                ->default(1)
                ->required(),

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

            TextInput::make('penalty_amount')
                ->label(FilamentUi::field('penalty_amount'))
                ->numeric()
                ->prefix('Rp')
                ->default(0),
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
                    ->numeric(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label(FilamentUi::field('unit_price'))
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('subtotal')
                    ->label(FilamentUi::field('subtotal'))
                    ->money('IDR'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['tenant_id'] = filament()->getTenant()?->id;

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
