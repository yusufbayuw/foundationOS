<x-filament-panels::page>
    <div class="space-y-4">
        @php($filters = $this->getFilterOptions())

        <form method="GET" class="grid gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:grid-cols-4">
            <label class="space-y-1 text-sm">
                <span class="font-medium text-gray-700 dark:text-gray-300">Module</span>
                <select name="module" class="w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    <option value="">All Modules</option>
                    @foreach ($filters['modules'] as $module)
                        <option value="{{ $module }}" @selected(request('module') === $module)>{{ $module }}</option>
                    @endforeach
                </select>
            </label>
            <label class="space-y-1 text-sm">
                <span class="font-medium text-gray-700 dark:text-gray-300">Workflow</span>
                <select name="workflow_id" class="w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    <option value="">All Workflows</option>
                    @foreach ($filters['workflows'] as $workflowId => $workflowName)
                        <option value="{{ $workflowId }}" @selected((string) request('workflow_id') === (string) $workflowId)>{{ $workflowName }}</option>
                    @endforeach
                </select>
            </label>
            <label class="space-y-1 text-sm">
                <span class="font-medium text-gray-700 dark:text-gray-300">SLA</span>
                <select name="sla" class="w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    <option value="">All</option>
                    <option value="due_today" @selected(request('sla') === 'due_today')>Due Today</option>
                    <option value="overdue" @selected(request('sla') === 'overdue')>Overdue</option>
                </select>
            </label>
            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex items-center rounded-lg bg-primary-600 px-3 py-2 text-sm font-medium text-white hover:bg-primary-700">
                    Apply
                </button>
                <a href="{{ request()->url() }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
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
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $title }}</h2>
                    @if ($title === 'Completed Recently')
                        <a href="{{ \Modules\Workflow\Filament\Pages\WorkflowTaskHistoryPage::getUrl() }}" class="text-xs font-medium text-primary-600 dark:text-primary-400">
                            View Full History
                        </a>
                    @endif
                </div>

                @if (count($assignments))
                    @foreach ($assignments as $assignment)
                        @php($summary = $this->summarizeAssignment($assignment))
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $summary['workflow'] }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $summary['module'] }} · {{ $summary['step'] }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Subject: {{ $summary['subject'] }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">
                                        Requester: {{ $summary['requester'] }} · Assigned: {{ $summary['assigned_at'] }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">
                                        Due: {{ $summary['due_at'] }} · SLA: {{ $summary['sla_status'] }}
                                    </p>
                                </div>
                                <a
                                    href="{{ \Modules\Workflow\Filament\Resources\WorkflowInstances\WorkflowInstanceResource::getUrl('view', ['record' => $assignment->instance]) }}"
                                    class="inline-flex items-center rounded-lg bg-primary-600 px-3 py-2 text-xs font-medium text-white hover:bg-primary-700"
                                >
                                    Open
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-6 text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400">
                        No items in this section.
                    </div>
                @endif
            </section>
        @endforeach
    </div>
</x-filament-panels::page>
