<?php

namespace Modules\Library\Filament\Resources\Members\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Models\User;
use Modules\Core\Support\FilamentUi;
use Modules\Library\Support\LibraryScopeResolver;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Scope & Identity')
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
                            ->helperText('Opsional. Kosongkan untuk member tenant-wide (lintas organisasi).'),
                        Select::make('user_id')
                            ->label(FilamentUi::field('user_id'))
                            ->relationship('user', 'name', modifyQueryUsing: function (Builder $query): void {
                                if (Filament::getTenant()) {
                                    $query->whereHas('userTenantRoles', fn (Builder $inner) => $inner->where('tenant_id', Filament::getTenant()->getKey()));
                                }
                            })
                            ->getOptionLabelFromRecordUsing(fn (User $record): string => $record->name.' ('.$record->email.')')
                            ->searchable(['name', 'email'])
                            ->preload()
                            ->required(),
                        TextInput::make('member_number')
                            ->label(FilamentUi::field('member_number'))
                            ->required(),
                    ]),

                Section::make('Membership')
                    ->columns(2)
                    ->schema([
                        Select::make('member_type_id')
                            ->label(FilamentUi::field('member_type_id'))
                            ->relationship('memberType', 'name', modifyQueryUsing: function (Builder $query): void {
                                app(LibraryScopeResolver::class)->apply($query, auth()->user());
                            })
                            ->searchable()
                            ->preload()
                            ->helperText('Opsional. Jika diisi, kebijakan peminjaman mengikuti tipe member.'),
                        TextInput::make('member_type')
                            ->label(FilamentUi::field('member_type')),
                        DatePicker::make('joined_at')
                            ->label(FilamentUi::field('joined_at')),
                        DatePicker::make('expires_at')
                            ->label(FilamentUi::field('expires_at')),
                    ]),

                Section::make('Loan Limits')
                    ->columns(2)
                    ->schema([
                        TextInput::make('max_books')
                            ->label(FilamentUi::field('max_books'))
                            ->required()
                            ->numeric()
                            ->default(3),
                        TextInput::make('loan_period_days')
                            ->label(FilamentUi::field('loan_period_days'))
                            ->required()
                            ->numeric()
                            ->default(7),
                        TextInput::make('fine_per_day')
                            ->label(FilamentUi::field('fine_per_day'))
                            ->required()
                            ->numeric()
                            ->default(1000),
                    ]),

                Section::make('Activity & Fines')
                    ->columns(2)
                    ->schema([
                        TextInput::make('total_loans_count')
                            ->label(FilamentUi::field('total_loans_count'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('current_loans_count')
                            ->label(FilamentUi::field('current_loans_count'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('total_fines')
                            ->label(FilamentUi::field('total_fines'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('unpaid_fines')
                            ->label(FilamentUi::field('unpaid_fines'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make('Status & Notes')
                    ->columns(2)
                    ->schema([
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('active'),
                        DatePicker::make('suspension_until')
                            ->label(FilamentUi::field('suspension_until')),
                        Textarea::make('suspension_reason')
                            ->label(FilamentUi::field('suspension_reason'))
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
