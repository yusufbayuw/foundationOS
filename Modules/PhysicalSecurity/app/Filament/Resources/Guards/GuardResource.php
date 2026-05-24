<?php

namespace Modules\PhysicalSecurity\Filament\Resources\Guards;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\PhysicalSecurity\Filament\Resources\Guards\Pages\CreateGuard;
use Modules\PhysicalSecurity\Filament\Resources\Guards\Pages\EditGuard;
use Modules\PhysicalSecurity\Filament\Resources\Guards\Pages\ListGuards;
use Modules\PhysicalSecurity\Filament\Resources\Guards\Pages\ViewGuard;
use Modules\PhysicalSecurity\Filament\Resources\Guards\Schemas\GuardForm;
use Modules\PhysicalSecurity\Filament\Resources\Guards\Schemas\GuardInfolist;
use Modules\PhysicalSecurity\Filament\Resources\Guards\Tables\GuardsTable;
use Modules\PhysicalSecurity\Models\Guard;

class GuardResource extends ModuleResource
{
    protected static ?string $model = Guard::class;

    public static function form(Schema $schema): Schema
    {
        return GuardForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GuardInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GuardsTable::configure($table);
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
            'index' => ListGuards::route('/'),
            'create' => CreateGuard::route('/create'),
            'view' => ViewGuard::route('/{record}'),
            'edit' => EditGuard::route('/{record}/edit'),
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
