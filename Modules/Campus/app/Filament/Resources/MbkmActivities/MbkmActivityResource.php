<?php

namespace Modules\Campus\Filament\Resources\MbkmActivities;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Campus\Filament\Resources\MbkmActivities\Pages\CreateMbkmActivity;
use Modules\Campus\Filament\Resources\MbkmActivities\Pages\EditMbkmActivity;
use Modules\Campus\Filament\Resources\MbkmActivities\Pages\ListMbkmActivities;
use Modules\Campus\Filament\Resources\MbkmActivities\Pages\ViewMbkmActivity;
use Modules\Campus\Filament\Resources\MbkmActivities\Schemas\MbkmActivityForm;
use Modules\Campus\Filament\Resources\MbkmActivities\Schemas\MbkmActivityInfolist;
use Modules\Campus\Filament\Resources\MbkmActivities\Tables\MbkmActivitiesTable;
use Modules\Campus\Models\MbkmActivity;
use Modules\Core\Filament\Support\ModuleResource;

class MbkmActivityResource extends ModuleResource
{
    protected static ?string $model = MbkmActivity::class;

    public static function form(Schema $schema): Schema
    {
        return MbkmActivityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MbkmActivityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MbkmActivitiesTable::configure($table);
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
            'index' => ListMbkmActivities::route('/'),
            'create' => CreateMbkmActivity::route('/create'),
            'view' => ViewMbkmActivity::route('/{record}'),
            'edit' => EditMbkmActivity::route('/{record}/edit'),
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
