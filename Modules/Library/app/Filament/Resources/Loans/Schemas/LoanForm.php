<?php

namespace Modules\Library\Filament\Resources\Loans\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Library\Support\LibraryScopeResolver;

class LoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Scope')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                                if (Filament::getTenant()) {
                                    $query->where('tenant_id', Filament::getTenant()->getKey());
                                }
                            })
                            ->nullable()
                            ->helperText('Opsional. Kosongkan untuk transaksi tenant-wide.'),
                    ]),

                Section::make('Loan Parties')
                    ->columns(2)
                    ->schema([
                        Select::make('book_copy_id')
                            ->label(FilamentUi::field('book_copy_id'))
                            ->relationship('bookCopy', 'copy_number', modifyQueryUsing: function (Builder $query): void {
                                app(LibraryScopeResolver::class)->apply($query, auth()->user());
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('member_id')
                            ->label(FilamentUi::field('member_id'))
                            ->relationship('member', 'member_number', modifyQueryUsing: function (Builder $query): void {
                                app(LibraryScopeResolver::class)->apply($query, auth()->user());
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('processed_by')
                            ->label(FilamentUi::field('processed_by'))
                            ->numeric(),
                        TextInput::make('returned_by')
                            ->label(FilamentUi::field('returned_by'))
                            ->numeric(),
                    ]),

                Section::make('Loan Period')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('loan_date')
                            ->label(FilamentUi::field('loan_date'))
                            ->required(),
                        DatePicker::make('due_date')
                            ->label(FilamentUi::field('due_date'))
                            ->required(),
                        DatePicker::make('return_date')
                            ->label(FilamentUi::field('return_date')),
                        TextInput::make('extension_count')
                            ->label(FilamentUi::field('extension_count'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('max_extensions')
                            ->label(FilamentUi::field('max_extensions'))
                            ->required()
                            ->numeric()
                            ->default(2),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('borrowed'),
                    ]),

                Section::make('Fines')
                    ->columns(2)
                    ->schema([
                        TextInput::make('fine_amount')
                            ->label(FilamentUi::field('fine_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('fine_paid')
                            ->label(FilamentUi::field('fine_paid'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('fine_status')
                            ->label(FilamentUi::field('fine_status'))
                            ->required()
                            ->default('none'),
                    ]),

                Section::make('Condition & Notes')
                    ->columns(2)
                    ->schema([
                        TextInput::make('condition_on_loan')
                            ->label(FilamentUi::field('condition_on_loan')),
                        TextInput::make('condition_on_return')
                            ->label(FilamentUi::field('condition_on_return')),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
