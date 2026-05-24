<?php

namespace Modules\Property\Filament\Resources\LeaseDeposits;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Property\Filament\Resources\LeaseDeposits\Pages\CreateLeaseDeposit;
use Modules\Property\Filament\Resources\LeaseDeposits\Pages\EditLeaseDeposit;
use Modules\Property\Filament\Resources\LeaseDeposits\Pages\ListLeaseDeposits;
use Modules\Property\Filament\Resources\LeaseDeposits\Pages\ViewLeaseDeposit;
use Modules\Property\Filament\Resources\LeaseDeposits\Schemas\LeaseDepositForm;
use Modules\Property\Filament\Resources\LeaseDeposits\Schemas\LeaseDepositInfolist;
use Modules\Property\Filament\Resources\LeaseDeposits\Tables\LeaseDepositsTable;
use Modules\Property\Models\LeaseDeposit;

class LeaseDepositResource extends ModuleResource
{
    protected static ?string $model = LeaseDeposit::class;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return LeaseDepositForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeaseDepositInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaseDepositsTable::configure($table);
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
            'index' => ListLeaseDeposits::route('/'),
            'create' => CreateLeaseDeposit::route('/create'),
            'view' => ViewLeaseDeposit::route('/{record}'),
            'edit' => EditLeaseDeposit::route('/{record}/edit'),
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
