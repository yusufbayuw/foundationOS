<?php

namespace Modules\Voucher\Filament\Resources\Vouchers;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Voucher\Filament\Resources\Vouchers\Pages\CreateVoucher;
use Modules\Voucher\Filament\Resources\Vouchers\Pages\EditVoucher;
use Modules\Voucher\Filament\Resources\Vouchers\Pages\ListVouchers;
use Modules\Voucher\Filament\Resources\Vouchers\Pages\ViewVoucher;
use Modules\Voucher\Filament\Resources\Vouchers\Schemas\VoucherForm;
use Modules\Voucher\Filament\Resources\Vouchers\Schemas\VoucherInfolist;
use Modules\Voucher\Filament\Resources\Vouchers\Tables\VouchersTable;
use Modules\Voucher\Models\Voucher;

class VoucherResource extends LocalizedResource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return VoucherForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VoucherInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VouchersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVouchers::route('/'),
            'create' => CreateVoucher::route('/create'),
            'view' => ViewVoucher::route('/{record}'),
            'edit' => EditVoucher::route('/{record}/edit'),
        ];
    }
}
