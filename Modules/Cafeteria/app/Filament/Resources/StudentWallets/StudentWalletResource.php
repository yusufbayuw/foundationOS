<?php

namespace Modules\Cafeteria\Filament\Resources\StudentWallets;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Cafeteria\Filament\Resources\StudentWallets\Pages\CreateStudentWallet;
use Modules\Cafeteria\Filament\Resources\StudentWallets\Pages\EditStudentWallet;
use Modules\Cafeteria\Filament\Resources\StudentWallets\Pages\ListStudentWallets;
use Modules\Cafeteria\Filament\Resources\StudentWallets\Pages\ViewStudentWallet;
use Modules\Cafeteria\Filament\Resources\StudentWallets\Schemas\StudentWalletForm;
use Modules\Cafeteria\Filament\Resources\StudentWallets\Schemas\StudentWalletInfolist;
use Modules\Cafeteria\Filament\Resources\StudentWallets\Tables\StudentWalletsTable;
use Modules\Cafeteria\Models\StudentWallet;
use Modules\Core\Filament\Support\ModuleResource;

class StudentWalletResource extends ModuleResource
{
    protected static ?string $model = StudentWallet::class;

    public static function form(Schema $schema): Schema
    {
        return StudentWalletForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentWalletInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentWalletsTable::configure($table);
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
            'index' => ListStudentWallets::route('/'),
            'create' => CreateStudentWallet::route('/create'),
            'view' => ViewStudentWallet::route('/{record}'),
            'edit' => EditStudentWallet::route('/{record}/edit'),
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
