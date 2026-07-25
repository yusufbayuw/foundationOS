<?php

namespace Modules\Voucher\Filament\Resources\VoucherClaims;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Voucher\Filament\Resources\VoucherClaims\Pages\ListVoucherClaims;
use Modules\Voucher\Filament\Resources\VoucherClaims\Tables\VoucherClaimsTable;
use Modules\Voucher\Models\VoucherClaim;

class VoucherClaimResource extends ModuleResource
{
    protected static ?string $model = VoucherClaim::class;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

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
