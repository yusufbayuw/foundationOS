<x-filament-panels::page>
    <div class="space-y-4">
        @forelse ($this->getHistory() as $assignment)
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <h3 class="text-sm font-semibold text-gray-900">
                            {{ $assignment->instance->workflow->name ?? 'Workflow' }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            Step: {{ $assignment->step->name ?? $assignment->instance->currentStep->name ?? '-' }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Subject: {{ $assignment->instance->subject_label ?: '-' }}
                        </p>
                        <p class="text-xs text-gray-400">
                            Status: {{ str($assignment->status->value ?? $assignment->status)->headline() }}
                        </p>
                        <p class="text-xs text-gray-400">
                            Completed At: {{ optional($assignment->completed_at ?? $assignment->updated_at)?->format('Y-m-d H:i') ?: '-' }}
                        </p>
                    </div>
                    <a
                        href="{{ \Modules\Workflow\Filament\Resources\WorkflowInstances\WorkflowInstanceResource::getUrl('view', ['record' => $assignment->instance]) }}"
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-xs font-medium text-white"
                    >
                        Open
                    </a>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-sm text-gray-500">
                No workflow task history found yet.
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
