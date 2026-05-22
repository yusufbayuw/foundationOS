<?php

namespace Modules\Workflow\Filament\Resources\WorkflowSteps;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Workflow\Filament\Resources\WorkflowSteps\Schemas\WorkflowStepForm;
use Modules\Workflow\Filament\Resources\WorkflowSteps\Tables\WorkflowStepsTable;
use Modules\Workflow\Models\WorkflowStep;

class WorkflowStepResource extends LocalizedResource
{
    protected static ?string $model = WorkflowStep::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return WorkflowStepForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkflowStepsTable::configure($table);
    }

    public static function isScopedToTenant(): bool
    {
        return false;
    }
}
