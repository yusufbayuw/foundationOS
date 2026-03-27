<?php

namespace Modules\Library\Filament\Resources\LibraryPolicies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Illuminate\Database\Eloquent\Builder;

class LibraryPolicyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TenantField::make(),
            Select::make('organization_id')
                ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                    if (Filament::getTenant()) {
                        $query->where('tenant_id', Filament::getTenant()->getKey());
                    }
                })
                ->nullable()
                ->helperText('Kosongkan agar policy berlaku tenant-wide.'),
            TextInput::make('name')->required(),
            TextInput::make('max_books')->numeric()->required()->default(3),
            TextInput::make('loan_period_days')->numeric()->required()->default(7),
            TextInput::make('fine_per_day')->numeric()->required()->default(1000),
            TextInput::make('max_extensions')->numeric()->required()->default(2),
            TextInput::make('grace_period_days')->numeric()->required()->default(0),
            TextInput::make('reservation_pickup_days')->numeric()->required()->default(2),
            Toggle::make('is_active')->default(true)->required(),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }
}
