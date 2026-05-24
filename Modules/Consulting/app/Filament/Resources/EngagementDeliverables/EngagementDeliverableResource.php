<?php

namespace Modules\Consulting\Filament\Resources\EngagementDeliverables;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Consulting\Filament\Resources\EngagementDeliverables\Pages\CreateEngagementDeliverable;
use Modules\Consulting\Filament\Resources\EngagementDeliverables\Pages\EditEngagementDeliverable;
use Modules\Consulting\Filament\Resources\EngagementDeliverables\Pages\ListEngagementDeliverables;
use Modules\Consulting\Filament\Resources\EngagementDeliverables\Pages\ViewEngagementDeliverable;
use Modules\Consulting\Filament\Resources\EngagementDeliverables\Schemas\EngagementDeliverableForm;
use Modules\Consulting\Filament\Resources\EngagementDeliverables\Schemas\EngagementDeliverableInfolist;
use Modules\Consulting\Filament\Resources\EngagementDeliverables\Tables\EngagementDeliverablesTable;
use Modules\Consulting\Models\EngagementDeliverable;
use Modules\Core\Filament\Support\ModuleResource;

class EngagementDeliverableResource extends ModuleResource
{
    protected static ?string $model = EngagementDeliverable::class;

    public static function form(Schema $schema): Schema
    {
        return EngagementDeliverableForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EngagementDeliverableInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EngagementDeliverablesTable::configure($table);
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
            'index' => ListEngagementDeliverables::route('/'),
            'create' => CreateEngagementDeliverable::route('/create'),
            'view' => ViewEngagementDeliverable::route('/{record}'),
            'edit' => EditEngagementDeliverable::route('/{record}/edit'),
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
