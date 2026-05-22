<div class="space-y-4"
    x-data="workflowDesigner(@js($steps), @js($transitions))"
    x-on:workflow-canvas:state-updated.window="onStateUpdated($event.detail)"
    x-on:workflow-canvas:download-json.window="downloadJson($event.detail)">

    {{-- Top bar --}}
    <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $workflowName ?: 'New Workflow' }}
                    </span>
                    <span @class([
                        'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium',
                        'bg-yellow-100 text-yellow-800' => $workflowStatus === 'draft',
                        'bg-green-100 text-green-800' => $workflowStatus === 'active',
                        'bg-gray-100 text-gray-600' => $workflowStatus === 'archived',
                    ])>{{ ucfirst($workflowStatus) }}</span>
                    @if ($isDirty)
                        <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-700">
                            Unsaved
                        </span>
                    @endif
                </div>
                @if ($workflowId)
                    <p class="text-xs text-gray-500">ID: {{ $workflowId }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button wire:click="saveDraft"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                Save Draft
            </button>

            @if ($workflowStatus === 'draft')
                <button wire:click="publish"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-primary-700">
                    <x-heroicon-o-rocket-launch class="h-4 w-4" />
                    Publish
                </button>
            @endif

            @if ($workflowId)
                <button wire:click="exportJson"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    <x-heroicon-o-arrow-up-tray class="h-4 w-4" />
                    Export JSON
                </button>
            @endif

            <button wire:click="addStep"
                class="inline-flex items-center gap-1.5 rounded-lg border border-dashed border-primary-400 bg-primary-50 px-3 py-1.5 text-sm font-medium text-primary-700 hover:bg-primary-100">
                <x-heroicon-o-plus class="h-4 w-4" />
                Add Step
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
        {{-- Canvas (3/4 width) --}}
        <div class="lg:col-span-3">
            {{-- Cytoscape canvas container --}}
            <div
                x-data="workflowCytoscape($wire, @js($steps), @js($transitions))"
                x-init="initCy()"
                x-on:workflow-canvas:state-updated.window="refreshGraph($event.detail.steps, $event.detail.transitions)"
                class="relative overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
                style="height: calc(100vh - 260px); min-height: 400px;"
            >
                <div x-ref="cytoscapeContainer" class="h-full w-full"></div>

                @if (empty($steps))
                    <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                        <x-heroicon-o-squares-2x2 class="mb-2 h-12 w-12" />
                        <p class="font-medium">Click "Add Step" to start designing</p>
                        <p class="text-xs">Drag nodes to rearrange the layout</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidepanel (1/4 width) --}}
        <div class="space-y-4">
            {{-- Workflow metadata --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Workflow</h3>
                <div class="space-y-3">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Name</label>
                        <input wire:model.live.debounce.500ms="workflowName" type="text"
                            class="w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Code</label>
                        <input wire:model.live.debounce.500ms="workflowCode" type="text"
                            class="w-full rounded-lg border-gray-300 font-mono text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Description</label>
                        <textarea wire:model.live.debounce.500ms="workflowDescription" rows="2"
                            class="w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>
                </div>
            </div>

            {{-- Selected step properties --}}
            @if ($selectedStepUuid)
                @php
                    $selectedStep = collect($steps)->firstWhere('uuid', $selectedStepUuid);
                @endphp
                @if ($selectedStep)
                    <div class="rounded-xl border border-primary-200 bg-white p-4 shadow-sm dark:border-primary-700 dark:bg-gray-800">
                        <h3 class="mb-3 text-sm font-semibold text-primary-700 dark:text-primary-400">
                            Step: {{ $selectedStep['name'] }}
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">Name</label>
                                <input type="text" value="{{ $selectedStep['name'] }}"
                                    x-on:change="$wire.updateStep('{{ $selectedStep['uuid'] }}', {name: $event.target.value})"
                                    class="w-full rounded-lg border-gray-300 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">Code</label>
                                <input type="text" value="{{ $selectedStep['code'] }}"
                                    x-on:change="$wire.updateStep('{{ $selectedStep['uuid'] }}', {code: $event.target.value})"
                                    class="w-full rounded-lg border-gray-300 font-mono text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">Step Type</label>
                                <select x-on:change="$wire.updateStep('{{ $selectedStep['uuid'] }}', {step_type: $event.target.value})"
                                    class="w-full rounded-lg border-gray-300 text-sm">
                                    @foreach (['start', 'task', 'approval', 'gateway', 'end'] as $type)
                                        <option value="{{ $type }}" {{ $selectedStep['step_type'] === $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">Gateway Type</label>
                                <select x-on:change="$wire.updateStep('{{ $selectedStep['uuid'] }}', {gateway_type: $event.target.value})"
                                    class="w-full rounded-lg border-gray-300 text-sm">
                                    @foreach (['none', 'parallel_split', 'parallel_join', 'inclusive', 'exclusive'] as $gtype)
                                        <option value="{{ $gtype }}" {{ $selectedStep['gateway_type'] === $gtype ? 'selected' : '' }}>
                                            {{ str_replace('_', ' ', ucfirst($gtype)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">SLA (hours)</label>
                                <input type="number" value="{{ $selectedStep['sla_hours'] ?? '' }}"
                                    x-on:change="$wire.updateStep('{{ $selectedStep['uuid'] }}', {sla_hours: $event.target.value ? parseInt($event.target.value) : null})"
                                    class="w-full rounded-lg border-gray-300 text-sm" />
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 text-xs font-medium text-gray-600">
                                    <input type="checkbox" {{ $selectedStep['is_initial'] ? 'checked' : '' }}
                                        x-on:change="$wire.updateStep('{{ $selectedStep['uuid'] }}', {is_initial: $event.target.checked})"
                                        class="rounded border-gray-300" />
                                    Initial
                                </label>
                                <label class="flex items-center gap-2 text-xs font-medium text-gray-600">
                                    <input type="checkbox" {{ $selectedStep['is_terminal'] ? 'checked' : '' }}
                                        x-on:change="$wire.updateStep('{{ $selectedStep['uuid'] }}', {is_terminal: $event.target.checked})"
                                        class="rounded border-gray-300" />
                                    Terminal
                                </label>
                            </div>
                            <button
                                wire:click="deleteStep('{{ $selectedStep['uuid'] }}')"
                                class="mt-1 w-full rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100">
                                Delete Step
                            </button>
                        </div>
                    </div>
                @endif
            @else
                <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800/50">
                    Click a node in the canvas to edit its properties
                </div>
            @endif

            {{-- Steps count --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>Steps</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ count($steps) }}</span>
                </div>
                <div class="mt-1 flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>Transitions</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ count($transitions) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@vite('resources/js/workflow-designer.js')
<script>
function workflowDesigner(initialSteps, initialTransitions) {
    return {
        steps: initialSteps,
        transitions: initialTransitions,

        onStateUpdated(detail) {
            if (detail.steps !== undefined) this.steps = detail.steps;
            if (detail.transitions !== undefined) this.transitions = detail.transitions;
        },

        downloadJson(detail) {
            const blob = new Blob([JSON.stringify(detail.payload, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = detail.filename || 'workflow.json';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        },
    };
}

function workflowCytoscape(wire, initialSteps, initialTransitions) {
    return {
        cy: null,

        initCy() {
            if (typeof window.cytoscape === 'undefined') {
                setTimeout(() => this.initCy(), 80);
                return;
            }

            const container = this.$refs.cytoscapeContainer;
            if (!container) return;

            const COLORS = {
                start: { bg: '#d1fae5', border: '#10b981', text: '#065f46' },
                end: { bg: '#fee2e2', border: '#ef4444', text: '#7f1d1d' },
                approval: { bg: '#dbeafe', border: '#3b82f6', text: '#1e3a8a' },
                task: { bg: '#f3f4f6', border: '#6b7280', text: '#111827' },
                gateway: { bg: '#ede9fe', border: '#8b5cf6', text: '#4c1d95' },
            };

            const buildElements = (steps, transitions) => {
                const nodes = (steps || []).map(s => {
                    const c = COLORS[s.step_type] || COLORS.task;
                    return {
                        data: { id: s.uuid, label: s.name + '\n[' + s.step_type + ']', bg: c.bg, border: c.border, textColor: c.text, stepData: s },
                        position: s.canvas_position ? { x: s.canvas_position.x, y: s.canvas_position.y } : { x: 0, y: 0 },
                    };
                });
                const edges = (transitions || [])
                    .filter(t => t.from_uuid && t.to_uuid)
                    .map((t, i) => ({ data: { id: 'e' + i, source: t.from_uuid, target: t.to_uuid, label: t.action_name || '' } }));
                return [...nodes, ...edges];
            };

            const applyLayout = (steps) => {
                if (!this.cy) return;
                const anyUnpositioned = (steps || []).some(s => !s.canvas_position);
                if (anyUnpositioned) {
                    this.cy.layout({ name: 'dagre', rankDir: 'LR', nodeSep: 60, rankSep: 120, padding: 40, animate: false }).run();
                    this.cy.nodes().forEach(n => {
                        const p = n.position();
                        wire.updateStepPosition(n.id(), p.x, p.y);
                    });
                }
            };

            this.cy = window.cytoscape({
                container,
                elements: buildElements(initialSteps, initialTransitions),
                style: [
                    { selector: 'node', style: { 'background-color': 'data(bg)', 'border-color': 'data(border)', 'border-width': 2, 'color': 'data(textColor)', label: 'data(label)', 'text-valign': 'center', 'text-halign': 'center', 'text-wrap': 'wrap', 'text-max-width': '120px', 'font-size': '11px', width: 140, height: 60, shape: 'roundrectangle' } },
                    { selector: 'node:selected', style: { 'border-width': 3, 'border-color': '#6366f1', 'overlay-color': '#6366f1', 'overlay-opacity': 0.1 } },
                    { selector: 'edge', style: { width: 2, 'line-color': '#94a3b8', 'target-arrow-color': '#94a3b8', 'target-arrow-shape': 'triangle', 'curve-style': 'bezier', label: 'data(label)', 'font-size': '10px', color: '#64748b', 'text-background-color': '#ffffff', 'text-background-opacity': 0.8, 'text-background-padding': '2px' } },
                ],
                layout: { name: 'preset' },
                userZoomingEnabled: true,
                userPanningEnabled: true,
            });

            applyLayout(initialSteps);

            this.cy.on('dragfree', 'node', e => {
                const pos = e.target.position();
                wire.updateStepPosition(e.target.id(), pos.x, pos.y);
            });

            this.cy.on('tap', 'node', e => {
                wire.selectStep(e.target.id());
            });

            this._buildElements = buildElements;
            this._applyLayout = applyLayout;
        },

        refreshGraph(steps, transitions) {
            if (!this.cy || !this._buildElements) return;
            this.cy.elements().remove();
            this.cy.add(this._buildElements(steps, transitions));
            this._applyLayout(steps);
        },
    };
}
</script>
@endpush
