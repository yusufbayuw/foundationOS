<?php

namespace Modules\Cafeteria\Filament\Resources\CafeteriaTransactions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Cafeteria\Filament\Resources\CafeteriaTransactions\Pages\CreateCafeteriaTransaction;
use Modules\Cafeteria\Filament\Resources\CafeteriaTransactions\Pages\EditCafeteriaTransaction;
use Modules\Cafeteria\Filament\Resources\CafeteriaTransactions\Pages\ListCafeteriaTransactions;
use Modules\Cafeteria\Filament\Resources\CafeteriaTransactions\Pages\ViewCafeteriaTransaction;
use Modules\Cafeteria\Filament\Resources\CafeteriaTransactions\Schemas\CafeteriaTransactionForm;
use Modules\Cafeteria\Filament\Resources\CafeteriaTransactions\Schemas\CafeteriaTransactionInfolist;
use Modules\Cafeteria\Filament\Resources\CafeteriaTransactions\Tables\CafeteriaTransactionsTable;
use Modules\Cafeteria\Models\CafeteriaTransaction;
use Modules\Core\Filament\Support\ModuleResource;

class CafeteriaTransactionResource extends ModuleResource
{
    protected static ?string $model = CafeteriaTransaction::class;

    public static function form(Schema $schema): Schema
    {
        return CafeteriaTransactionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CafeteriaTransactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CafeteriaTransactionsTable::configure($table);
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
            'index' => ListCafeteriaTransactions::route('/'),
            'create' => CreateCafeteriaTransaction::route('/create'),
            'view' => ViewCafeteriaTransaction::route('/{record}'),
            'edit' => EditCafeteriaTransaction::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
