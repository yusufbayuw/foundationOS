<?php

namespace Modules\InternalAudit\Filament\Resources\CorrectiveActions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\InternalAudit\Filament\Resources\CorrectiveActions\Pages\CreateCorrectiveAction;
use Modules\InternalAudit\Filament\Resources\CorrectiveActions\Pages\EditCorrectiveAction;
use Modules\InternalAudit\Filament\Resources\CorrectiveActions\Pages\ListCorrectiveActions;
use Modules\InternalAudit\Filament\Resources\CorrectiveActions\Pages\ViewCorrectiveAction;
use Modules\InternalAudit\Filament\Resources\CorrectiveActions\Schemas\CorrectiveActionForm;
use Modules\InternalAudit\Filament\Resources\CorrectiveActions\Schemas\CorrectiveActionInfolist;
use Modules\InternalAudit\Filament\Resources\CorrectiveActions\Tables\CorrectiveActionsTable;
use Modules\InternalAudit\Models\CorrectiveAction;

class CorrectiveActionResource extends ModuleResource
{
    protected static ?string $model = CorrectiveAction::class;

    public static function form(Schema $schema): Schema
    {
        return CorrectiveActionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CorrectiveActionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CorrectiveActionsTable::configure($table);
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
            'index' => ListCorrectiveActions::route('/'),
            'create' => CreateCorrectiveAction::route('/create'),
            'view' => ViewCorrectiveAction::route('/{record}'),
            'edit' => EditCorrectiveAction::route('/{record}/edit'),
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
