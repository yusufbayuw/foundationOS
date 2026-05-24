<?php

namespace Modules\Workflow\Filament\Resources\WorkflowEvidences;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Workflow\Filament\Resources\WorkflowEvidences\Pages\CreateWorkflowEvidence;
use Modules\Workflow\Filament\Resources\WorkflowEvidences\Pages\EditWorkflowEvidence;
use Modules\Workflow\Filament\Resources\WorkflowEvidences\Pages\ListWorkflowEvidences;
use Modules\Workflow\Filament\Resources\WorkflowEvidences\Pages\ViewWorkflowEvidence;
use Modules\Workflow\Filament\Resources\WorkflowEvidences\Schemas\WorkflowEvidenceForm;
use Modules\Workflow\Filament\Resources\WorkflowEvidences\Schemas\WorkflowEvidenceInfolist;
use Modules\Workflow\Filament\Resources\WorkflowEvidences\Tables\WorkflowEvidencesTable;
use Modules\Workflow\Models\WorkflowEvidence;

class WorkflowEvidenceResource extends ModuleResource
{
    protected static ?string $model = WorkflowEvidence::class;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return WorkflowEvidenceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkflowEvidenceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkflowEvidencesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkflowEvidences::route('/'),
            'create' => CreateWorkflowEvidence::route('/create'),
            'view' => ViewWorkflowEvidence::route('/{record}'),
            'edit' => EditWorkflowEvidence::route('/{record}/edit'),
        ];
    }
}
