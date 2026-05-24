<?php

namespace Modules\Asset\Filament\Resources\AssetLoans;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Asset\Filament\Resources\AssetLoans\Pages\CreateAssetLoan;
use Modules\Asset\Filament\Resources\AssetLoans\Pages\EditAssetLoan;
use Modules\Asset\Filament\Resources\AssetLoans\Pages\ListAssetLoans;
use Modules\Asset\Filament\Resources\AssetLoans\Pages\ViewAssetLoan;
use Modules\Asset\Filament\Resources\AssetLoans\Schemas\AssetLoanForm;
use Modules\Asset\Filament\Resources\AssetLoans\Schemas\AssetLoanInfolist;
use Modules\Asset\Filament\Resources\AssetLoans\Tables\AssetLoansTable;
use Modules\Asset\Models\AssetLoan;
use Modules\Core\Filament\Support\ModuleResource;

class AssetLoanResource extends ModuleResource
{
    protected static ?string $model = AssetLoan::class;

    public static function form(Schema $schema): Schema
    {
        return AssetLoanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssetLoanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetLoansTable::configure($table);
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
            'index' => ListAssetLoans::route('/'),
            'create' => CreateAssetLoan::route('/create'),
            'view' => ViewAssetLoan::route('/{record}'),
            'edit' => EditAssetLoan::route('/{record}/edit'),
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
