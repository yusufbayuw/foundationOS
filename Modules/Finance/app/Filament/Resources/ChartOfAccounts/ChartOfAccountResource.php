<?php

namespace Modules\Finance\Filament\Resources\ChartOfAccounts;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\ChartOfAccounts\Pages\CreateChartOfAccount;
use Modules\Finance\Filament\Resources\ChartOfAccounts\Pages\EditChartOfAccount;
use Modules\Finance\Filament\Resources\ChartOfAccounts\Pages\ListChartOfAccounts;
use Modules\Finance\Filament\Resources\ChartOfAccounts\Pages\ViewChartOfAccount;
use Modules\Finance\Filament\Resources\ChartOfAccounts\Schemas\ChartOfAccountForm;
use Modules\Finance\Filament\Resources\ChartOfAccounts\Schemas\ChartOfAccountInfolist;
use Modules\Finance\Filament\Resources\ChartOfAccounts\Tables\ChartOfAccountsTable;
use Modules\Finance\Models\ChartOfAccount;

class ChartOfAccountResource extends LocalizedResource
{
    protected static ?string $model = ChartOfAccount::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ChartOfAccountForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChartOfAccountInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChartOfAccountsTable::configure($table);
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
            'index' => ListChartOfAccounts::route('/'),
            'create' => CreateChartOfAccount::route('/create'),
            'view' => ViewChartOfAccount::route('/{record}'),
            'edit' => EditChartOfAccount::route('/{record}/edit'),
        ];
    }
}
