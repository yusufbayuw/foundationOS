<?php

namespace Modules\Library\Filament\Resources\Members\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\User;

class MemberForm
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
                    ->helperText('Opsional. Kosongkan untuk member tenant-wide (lintas organisasi).'),
                Select::make('user_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('user_id'))
                    ->relationship('user', 'name', modifyQueryUsing: function (Builder $query): void {
                        if (Filament::getTenant()) {
                            $query->whereHas('userTenantRoles', fn (Builder $inner) => $inner->where('tenant_id', Filament::getTenant()->getKey()));
                        }
                    })
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => $record->name . ' (' . $record->email . ')')
                    ->searchable(['name', 'email'])
                    ->preload()
                    ->required(),
                TextInput::make('member_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('member_number'))
                    ->required(),
                Select::make('member_type_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('member_type_id'))
                    ->relationship('memberType', 'name', modifyQueryUsing: function (Builder $query): void {
                        app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                    })
                    ->searchable()
                    ->preload()
                    ->helperText('Opsional. Jika diisi, kebijakan peminjaman mengikuti tipe member.'),
                TextInput::make('member_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('member_type')),
                DatePicker::make('joined_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('joined_at')),
                DatePicker::make('expires_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('expires_at')),
                TextInput::make('max_books')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_books'))
                    ->required()
                    ->numeric()
                    ->default(3),
                TextInput::make('loan_period_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('loan_period_days'))
                    ->required()
                    ->numeric()
                    ->default(7),
                TextInput::make('fine_per_day')
                    ->label(\Modules\Core\Support\FilamentUi::field('fine_per_day'))
                    ->required()
                    ->numeric()
                    ->default(1000),
                TextInput::make('total_loans_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_loans_count'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('current_loans_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('current_loans_count'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_fines')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_fines'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('unpaid_fines')
                    ->label(\Modules\Core\Support\FilamentUi::field('unpaid_fines'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('active'),
                Textarea::make('suspension_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('suspension_reason'))
                    ->columnSpanFull(),
                DatePicker::make('suspension_until')
                    ->label(\Modules\Core\Support\FilamentUi::field('suspension_until')),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
