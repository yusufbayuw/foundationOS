<?php

namespace Modules\ItOps\Filament\Resources\UserAccounts;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\ItOps\Filament\Resources\UserAccounts\Pages\CreateUserAccount;
use Modules\ItOps\Filament\Resources\UserAccounts\Pages\EditUserAccount;
use Modules\ItOps\Filament\Resources\UserAccounts\Pages\ListUserAccounts;
use Modules\ItOps\Filament\Resources\UserAccounts\Pages\ViewUserAccount;
use Modules\ItOps\Filament\Resources\UserAccounts\Schemas\UserAccountForm;
use Modules\ItOps\Filament\Resources\UserAccounts\Schemas\UserAccountInfolist;
use Modules\ItOps\Filament\Resources\UserAccounts\Tables\UserAccountsTable;
use Modules\ItOps\Models\UserAccount;

class UserAccountResource extends ModuleResource
{
    protected static ?string $model = UserAccount::class;

    public static function form(Schema $schema): Schema
    {
        return UserAccountForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserAccountInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserAccountsTable::configure($table);
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
            'index' => ListUserAccounts::route('/'),
            'create' => CreateUserAccount::route('/create'),
            'view' => ViewUserAccount::route('/{record}'),
            'edit' => EditUserAccount::route('/{record}/edit'),
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
