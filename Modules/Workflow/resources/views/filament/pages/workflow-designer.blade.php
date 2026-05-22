<x-filament-panels::page>
    <div class="space-y-2">
        @livewire('workflow-canvas', ['workflowId' => $this->workflowId])
    </div>
</x-filament-panels::page>
