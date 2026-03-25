<?php

namespace Modules\Finance\Filament\Resources\Payments;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\Payments\Pages\CreatePayment;
use Modules\Finance\Filament\Resources\Payments\Pages\EditPayment;
use Modules\Finance\Filament\Resources\Payments\Pages\ListPayments;
use Modules\Finance\Filament\Resources\Payments\Pages\ViewPayment;
use Modules\Finance\Filament\Resources\Payments\Schemas\PaymentForm;
use Modules\Finance\Filament\Resources\Payments\Schemas\PaymentInfolist;
use Modules\Finance\Filament\Resources\Payments\Tables\PaymentsTable;
use Modules\Finance\Models\Payment;

class PaymentResource extends LocalizedResource
{
    protected static ?string $model = Payment::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PaymentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'view' => ViewPayment::route('/{record}'),
            'edit' => EditPayment::route('/{record}/edit'),
        ];
    }
}
