<?php

namespace Modules\Library\Filament\Resources\LibraryPolicies\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class LibraryPolicyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('Scope'))
                ->columns(2)
                ->schema([
                    TenantField::make(),
                    Select::make('organization_id')
                        ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                            if (Filament::getTenant()) {
                                $query->where('tenant_id', Filament::getTenant()->getKey());
                            }
                        })
                        ->nullable()
                        ->helperText(FilamentUi::text('Leave blank for tenant-wide policy.')),
                    TextInput::make('name')->required(),
                ]),

            Section::make(FilamentUi::text('Loan Limits'))
                ->columns(2)
                ->schema([
                    TextInput::make('max_books')->numeric()->required()->default(3),
                    TextInput::make('loan_period_days')->numeric()->required()->default(7),
                    TextInput::make('fine_per_day')->numeric()->required()->default(1000),
                    TextInput::make('max_extensions')->numeric()->required()->default(2),
                    TextInput::make('grace_period_days')->numeric()->required()->default(0),
                    TextInput::make('reservation_pickup_days')->numeric()->required()->default(2),
                ]),

            Section::make(FilamentUi::text('Status & Notes'))
                ->columns(2)
                ->schema([
                    Toggle::make('is_active')->default(true)->required(),
                    Textarea::make('notes')->columnSpanFull(),
                ]),
        ]);
    }
}
