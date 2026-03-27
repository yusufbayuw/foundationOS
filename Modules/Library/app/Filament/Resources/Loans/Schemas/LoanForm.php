<?php

namespace Modules\Library\Filament\Resources\Loans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Illuminate\Database\Eloquent\Builder;

class LoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                        if (Filament::getTenant()) {
                            $query->where('tenant_id', Filament::getTenant()->getKey());
                        }
                    })
                    ->nullable()
                    ->helperText('Opsional. Kosongkan untuk transaksi tenant-wide.'),
                Select::make('book_copy_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('book_copy_id'))
                    ->relationship('bookCopy', 'copy_number', modifyQueryUsing: function (Builder $query): void {
                        app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('member_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('member_id'))
                    ->relationship('member', 'member_number', modifyQueryUsing: function (Builder $query): void {
                        app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('processed_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('processed_by'))
                    ->numeric(),
                TextInput::make('returned_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('returned_by'))
                    ->numeric(),
                DatePicker::make('loan_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('loan_date'))
                    ->required(),
                DatePicker::make('due_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('due_date'))
                    ->required(),
                DatePicker::make('return_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('return_date')),
                TextInput::make('extension_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('extension_count'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('max_extensions')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_extensions'))
                    ->required()
                    ->numeric()
                    ->default(2),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('borrowed'),
                TextInput::make('fine_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('fine_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('fine_paid')
                    ->label(\Modules\Core\Support\FilamentUi::field('fine_paid'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('fine_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('fine_status'))
                    ->required()
                    ->default('none'),
                TextInput::make('condition_on_loan')
                    ->label(\Modules\Core\Support\FilamentUi::field('condition_on_loan')),
                TextInput::make('condition_on_return')
                    ->label(\Modules\Core\Support\FilamentUi::field('condition_on_return')),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
