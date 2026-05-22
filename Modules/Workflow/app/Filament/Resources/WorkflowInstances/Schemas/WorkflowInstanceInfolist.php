<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstances\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class WorkflowInstanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('workflow.name')->label(FilamentUi::text('Workflow')),
            TextEntry::make('subject_label')->label(FilamentUi::field('subject_label'))->placeholder('-'),
            TextEntry::make('subject_type')->label(FilamentUi::field('subject_type'))->formatStateUsing(fn (?string $state): string => (string) str(class_basename((string) $state))->headline()),
            TextEntry::make('status')->label(FilamentUi::field('status'))->badge(),
            TextEntry::make('requester.name')->label(FilamentUi::text('Requester')),
            TextEntry::make('currentStep.name')->label(FilamentUi::text('Current step'))->placeholder('-'),
            TextEntry::make('started_at')->label(FilamentUi::field('started_at'))->dateTime(),
            TextEntry::make('due_at')->label(FilamentUi::field('due_at'))->dateTime()->placeholder('-'),
            TextEntry::make('completed_at')->label(FilamentUi::text('Completed at'))->dateTime()->placeholder('-'),
            TextEntry::make('context_data.request_number')->label(FilamentUi::text('Request number'))->placeholder('-'),
            TextEntry::make('context_data.budget_code')->label(FilamentUi::text('Budget code'))->placeholder('-'),
            TextEntry::make('context_data.total_estimated_amount')->label(FilamentUi::text('Requested amount'))->numeric(decimalPlaces: 2)->placeholder('-'),
            TextEntry::make('context_data.allocated_amount')->label(FilamentUi::text('Allocated amount'))->numeric(decimalPlaces: 2)->placeholder('-'),
            KeyValueEntry::make('context_data')->label(FilamentUi::text('Context data'))->columnSpanFull(),
            KeyValueEntry::make('form_data')->label(FilamentUi::text('Form data'))->columnSpanFull(),
            KeyValueEntry::make('computed_data')->label(FilamentUi::text('Computed data'))->columnSpanFull(),
            KeyValueEntry::make('current_assignees')->label(FilamentUi::text('Current assignees'))->columnSpanFull(),
        ]);
    }
}
