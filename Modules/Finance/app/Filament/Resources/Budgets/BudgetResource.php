<?php

namespace Modules\Finance\Filament\Resources\Budgets;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Finance\Filament\Resources\Budgets\Pages\CreateBudget;
use Modules\Finance\Filament\Resources\Budgets\Pages\EditBudget;
use Modules\Finance\Filament\Resources\Budgets\Pages\ListBudgets;
use Modules\Finance\Filament\Resources\Budgets\Pages\ViewBudget;
use Modules\Finance\Filament\Resources\Budgets\RelationManagers\WorkflowInstancesRelationManager;
use Modules\Finance\Filament\Resources\Budgets\Schemas\BudgetForm;
use Modules\Finance\Filament\Resources\Budgets\Schemas\BudgetInfolist;
use Modules\Finance\Filament\Resources\Budgets\Tables\BudgetsTable;
use Modules\Finance\Models\Budget;

class BudgetResource extends LocalizedResource
{
    protected static ?string $model = Budget::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function form(Schema $schema): Schema
    {
        return BudgetForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BudgetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BudgetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            WorkflowInstancesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBudgets::route('/'),
            'create' => CreateBudget::route('/create'),
            'view' => ViewBudget::route('/{record}'),
            'edit' => EditBudget::route('/{record}/edit'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return $record instanceof Budget
            && ! $record->isLockedForMutation()
            && parent::canEdit($record);
    }
}
