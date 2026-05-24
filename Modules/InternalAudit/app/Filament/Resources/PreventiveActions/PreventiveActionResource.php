<?php

namespace Modules\InternalAudit\Filament\Resources\PreventiveActions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\InternalAudit\Filament\Resources\PreventiveActions\Pages\CreatePreventiveAction;
use Modules\InternalAudit\Filament\Resources\PreventiveActions\Pages\EditPreventiveAction;
use Modules\InternalAudit\Filament\Resources\PreventiveActions\Pages\ListPreventiveActions;
use Modules\InternalAudit\Filament\Resources\PreventiveActions\Pages\ViewPreventiveAction;
use Modules\InternalAudit\Filament\Resources\PreventiveActions\Schemas\PreventiveActionForm;
use Modules\InternalAudit\Filament\Resources\PreventiveActions\Schemas\PreventiveActionInfolist;
use Modules\InternalAudit\Filament\Resources\PreventiveActions\Tables\PreventiveActionsTable;
use Modules\InternalAudit\Models\PreventiveAction;

class PreventiveActionResource extends ModuleResource
{
    protected static ?string $model = PreventiveAction::class;

    public static function form(Schema $schema): Schema
    {
        return PreventiveActionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PreventiveActionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PreventiveActionsTable::configure($table);
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
            'index' => ListPreventiveActions::route('/'),
            'create' => CreatePreventiveAction::route('/create'),
            'view' => ViewPreventiveAction::route('/{record}'),
            'edit' => EditPreventiveAction::route('/{record}/edit'),
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
