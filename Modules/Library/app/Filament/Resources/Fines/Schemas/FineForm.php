<?php

namespace Modules\Library\Filament\Resources\Fines\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class FineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->default(Filament::getTenant()?->getKey())
                    ->disabled(Filament::getTenant() !== null)
                    ->dehydrated()
                    ->required(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                        if (Filament::getTenant()) {
                            $query->where('tenant_id', Filament::getTenant()->getKey());
                        }
                    })
                    ->nullable()
                    ->helperText('Opsional. Kosongkan untuk denda tenant-wide.'),
                Select::make('loan_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('loan_id'))
                    ->relationship('loan', 'id', modifyQueryUsing: function (Builder $query): void {
                        app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('fine_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('fine_type'))
                    ->required()
                    ->default('late_return'),
                TextInput::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->required()
                    ->numeric(),
                TextInput::make('paid_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('paid_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('unpaid'),
                DatePicker::make('issued_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('issued_at')),
                DatePicker::make('paid_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('paid_at')),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
