<?php

namespace Modules\IsoCompliance\Filament\Resources\IsoPolicies;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\IsoCompliance\Filament\Resources\IsoPolicies\Pages\CreateIsoPolicy;
use Modules\IsoCompliance\Filament\Resources\IsoPolicies\Pages\EditIsoPolicy;
use Modules\IsoCompliance\Filament\Resources\IsoPolicies\Pages\ListIsoPolicies;
use Modules\IsoCompliance\Filament\Resources\IsoPolicies\Pages\ViewIsoPolicy;
use Modules\IsoCompliance\Filament\Resources\IsoPolicies\Schemas\IsoPolicyForm;
use Modules\IsoCompliance\Filament\Resources\IsoPolicies\Schemas\IsoPolicyInfolist;
use Modules\IsoCompliance\Filament\Resources\IsoPolicies\Tables\IsoPoliciesTable;
use Modules\IsoCompliance\Models\IsoPolicy;

class IsoPolicyResource extends ModuleResource
{
    protected static ?string $model = IsoPolicy::class;

    public static function form(Schema $schema): Schema
    {
        return IsoPolicyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IsoPolicyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IsoPoliciesTable::configure($table);
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
            'index' => ListIsoPolicies::route('/'),
            'create' => CreateIsoPolicy::route('/create'),
            'view' => ViewIsoPolicy::route('/{record}'),
            'edit' => EditIsoPolicy::route('/{record}/edit'),
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
