<x-filament-panels::page>
    <div class="space-y-4">
        @php($filters = $this->getFilterOptions())

        <form method="GET" class="grid gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-4">
            <label class="space-y-1 text-sm">
                <span class="font-medium text-gray-700">Module</span>
                <select name="module" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">All Modules</option>
                    @foreach ($filters['modules'] as $module)
                        <option value="{{ $module }}" @selected(request('module') === $module)>{{ $module }}</option>
                    @endforeach
                </select>
            </label>
            <label class="space-y-1 text-sm">
                <span class="font-medium text-gray-700">Workflow</span>
                <select name="workflow_id" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">All Workflows</option>
                    @foreach ($filters['workflows'] as $workflowId => $workflowName)
                        <option value="{{ $workflowId }}" @selected((string) request('workflow_id') === (string) $workflowId)>{{ $workflowName }}</option>
                    @endforeach
                </select>
            </label>
            <label class="space-y-1 text-sm">
                <span class="font-medium text-gray-700">SLA</span>
                <select name="sla" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">All</option>
                    <option value="due_today" @selected(request('sla') === 'due_today')>Due Today</option>
                    <option value="overdue" @selected(request('sla') === 'overdue')>Overdue</option>
                </select>
            </label>
            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white">
                    Apply
                </button>
                <a href="{{ request()->url() }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700">
                    Reset
                </a>
            </div>
        </form>

        @php($sections = [
            'My Tasks' => $this->getMyTasks(),
            'Overdue' => $this->getOverdueTasks(),
            'Delegated / Reassigned' => $this->getDelegatedTasks(),
            'Completed Recently' => $this->getCompletedRecently(),
        ])

        @foreach ($sections as $title => $assignments)
            <section class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900">{{ $title }}</h2>
                    @if ($title === 'Completed Recently')
                        <a href="{{ \Modules\Workflow\Filament\Pages\WorkflowTaskHistoryPage::getUrl() }}" class="text-xs font-medium text-indigo-600">
                            View Full History
                        </a>
                    @endif
                </div>

                @if (count($assignments))
                    @foreach ($assignments as $assignment)
                        @php($summary = $this->summarizeAssignment($assignment))
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1">
                                    <h3 class="text-sm font-semibold text-gray-900">
                                        {{ $summary['workflow'] }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $summary['module'] }} · {{ $summary['step'] }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Subject: {{ $summary['subject'] }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        Requester: {{ $summary['requester'] }} · Assigned: {{ $summary['assigned_at'] }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        Due: {{ $summary['due_at'] }} · SLA: {{ $summary['sla_status'] }}
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
                    @endforeach
                @else
                    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-6 text-sm text-gray-500">
                        No items in this section.
                    </div>
                @endif
            </section>
        @endforeach
    </div>
</x-filament-panels::page>
