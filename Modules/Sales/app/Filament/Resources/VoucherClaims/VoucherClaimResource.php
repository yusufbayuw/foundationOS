<?php

namespace Modules\Sales\Filament\Resources\VoucherClaims;

use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Sales\Filament\Resources\VoucherClaims\Pages\ListVoucherClaims;
use Modules\Sales\Filament\Resources\VoucherClaims\Tables\VoucherClaimsTable;
use Modules\Sales\Models\VoucherClaim;

class VoucherClaimResource extends ModuleResource
{
    protected static ?string $model = VoucherClaim::class;

    protected static ?string $recordTitleAttribute = 'claim_code';

    public static function table(Table $table): Table
    {
        return VoucherClaimsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVoucherClaims::route('/'),
        ];
    }
}
