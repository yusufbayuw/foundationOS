<?php

namespace Modules\Workflow\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Models\Workflow;

class WorkflowDesignerPage extends Page
{
    use HasPageShield;

    protected static string $routePath = '/workflow/designer/{workflowId?}';

    protected string $view = 'workflow::filament.pages.workflow-designer';

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Squares2x2;

    protected static ?int $navigationSort = 35;

    public ?int $workflowId = null;

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Workflow designer');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return FilamentUi::module('Workflow');
    }

    public function mount(?int $workflowId = null): void
    {
        $this->workflowId = $workflowId;
    }

    public function getTitle(): string
    {
        if ($this->workflowId) {
            $workflow = Workflow::find($this->workflowId);

            return ($workflow?->name ?? FilamentUi::text('Workflow')).' — '.FilamentUi::text('Workflow designer');
        }

        return FilamentUi::text('New workflow').' — '.FilamentUi::text('Workflow designer');
    }
}
