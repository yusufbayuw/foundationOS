<?php

namespace Modules\Workflow\Filament\Pages;

use Modules\Core\Support\FilamentUi;

class WorkflowMyTasksPage extends WorkflowInboxPage
{
    protected static string $routePath = '/workflow/my-tasks';

    protected static ?int $navigationSort = 31;

    public static function getNavigationLabel(): string
    {
        return 'Workflow My Tasks';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return FilamentUi::module('Workflow');
    }
}
