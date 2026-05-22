<?php

namespace Modules\Workflow\Filament\Resources\WorkflowTransitions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Workflow\Filament\Resources\WorkflowTransitions\Schemas\WorkflowTransitionForm;
use Modules\Workflow\Filament\Resources\WorkflowTransitions\Tables\WorkflowTransitionsTable;
use Modules\Workflow\Models\WorkflowTransition;

class WorkflowTransitionResource extends LocalizedResource
{
    protected static ?string $model = WorkflowTransition::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return WorkflowTransitionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkflowTransitionsTable::configure($table);
    }

    public static function isScopedToTenant(): bool
    {
        return false;
    }
}
