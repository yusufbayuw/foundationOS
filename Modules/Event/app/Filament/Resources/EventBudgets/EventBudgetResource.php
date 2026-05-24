<?php

namespace Modules\Event\Filament\Resources\EventBudgets;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Event\Filament\Resources\EventBudgets\Pages\CreateEventBudget;
use Modules\Event\Filament\Resources\EventBudgets\Pages\EditEventBudget;
use Modules\Event\Filament\Resources\EventBudgets\Pages\ListEventBudgets;
use Modules\Event\Filament\Resources\EventBudgets\Pages\ViewEventBudget;
use Modules\Event\Filament\Resources\EventBudgets\Schemas\EventBudgetForm;
use Modules\Event\Filament\Resources\EventBudgets\Schemas\EventBudgetInfolist;
use Modules\Event\Filament\Resources\EventBudgets\Tables\EventBudgetsTable;
use Modules\Event\Models\EventBudget;

class EventBudgetResource extends ModuleResource
{
    protected static ?string $model = EventBudget::class;

    public static function form(Schema $schema): Schema
    {
        return EventBudgetForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EventBudgetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventBudgetsTable::configure($table);
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
            'index' => ListEventBudgets::route('/'),
            'create' => CreateEventBudget::route('/create'),
            'view' => ViewEventBudget::route('/{record}'),
            'edit' => EditEventBudget::route('/{record}/edit'),
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
