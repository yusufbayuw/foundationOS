<?php

namespace Modules\Workflow\Services;

use Illuminate\Support\Facades\DB;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Exceptions\WorkflowConfigurationException;
use Modules\Workflow\Models\Workflow;

class WorkflowDefinitionLifecycleService
{
    public function __construct(
        private readonly WorkflowDefinitionGraphMaterializer $graphMaterializer,
    ) {}

    /**
     * @throws WorkflowConfigurationException
     */
    public function publish(Workflow $workflow, ?int $actorId = null): Workflow
    {
        return DB::transaction(function () use ($workflow, $actorId): Workflow {
            $workflow = $workflow->fresh(['steps', 'transitions']);

            $this->assertPublishable($workflow);

            Workflow::query()
                ->where('tenant_id', $workflow->tenant_id)
                ->where('code', $workflow->code)
                ->where('id', '!=', $workflow->id)
                ->where(function ($query) use ($workflow): void {
                    if ($workflow->organization_id) {
                        $query->where('organization_id', $workflow->organization_id);
                    } else {
                        $query->whereNull('organization_id');
                    }
                })
                ->update([
                    'status' => WorkflowDefinitionStatus::Archived,
                    'is_active' => false,
                    'updated_by' => $actorId,
                    'updated_at' => now(),
                ]);

            $workflow->forceFill([
                'status' => WorkflowDefinitionStatus::Active,
                'is_active' => true,
                'published_at' => $workflow->published_at ?? now(),
                'updated_by' => $actorId,
            ])->save();

            return $workflow->fresh();
        });
    }

    public function archive(Workflow $workflow, ?int $actorId = null): Workflow
    {
        $workflow->forceFill([
            'status' => WorkflowDefinitionStatus::Archived,
            'is_active' => false,
            'updated_by' => $actorId,
        ])->save();

        return $workflow->fresh();
    }

    public function duplicateAsNewVersion(Workflow $workflow, ?int $actorId = null): Workflow
    {
        return DB::transaction(function () use ($workflow, $actorId): Workflow {
            $workflow = $workflow->fresh(['steps', 'transitions', 'automatedActions']);

            $nextVersion = (int) Workflow::query()
                ->where('tenant_id', $workflow->tenant_id)
                ->where('code', $workflow->code)
                ->where(function ($query) use ($workflow): void {
                    if ($workflow->organization_id) {
                        $query->where('organization_id', $workflow->organization_id);
                    } else {
                        $query->whereNull('organization_id');
                    }
                })
                ->max('version') + 1;

            $clone = Workflow::query()->create([
                'tenant_id' => $workflow->tenant_id,
                'organization_id' => $workflow->organization_id,
                'code' => $workflow->code,
                'name' => $workflow->name,
                'description' => $workflow->description,
                'module' => $workflow->module,
                'subject_type' => $workflow->subject_type,
                'trigger_mode' => $workflow->trigger_mode,
                'version' => $nextVersion,
                'status' => WorkflowDefinitionStatus::Draft,
                'is_active' => false,
                'published_at' => null,
                'created_by' => $actorId,
                'updated_by' => $actorId,
            ]);

            $stepIdMap = $this->graphMaterializer->copyStepsFromWorkflow($workflow, $clone);
            $this->graphMaterializer->copyTransitionsFromWorkflow($workflow, $clone, $stepIdMap);
            $this->graphMaterializer->copyAutomatedActionsFromWorkflow($workflow, $clone, $stepIdMap);

            return $clone->fresh(['steps', 'transitions', 'automatedActions']);
        });
    }

    /**
     * Assert a workflow is structurally valid before publishing.
     *
     * @throws WorkflowConfigurationException
     */
    public function assertPublishable(Workflow $workflow): void
    {
        $steps = $workflow->steps()->get();

        $initialCount = $steps->where('is_initial', true)->count();
        if ($initialCount !== 1) {
            throw new WorkflowConfigurationException(
                "Workflow [{$workflow->code}] must have exactly 1 initial step, found {$initialCount}."
            );
        }

        $terminalCount = $steps->where('is_terminal', true)->count();
        if ($terminalCount < 1) {
            throw new WorkflowConfigurationException(
                "Workflow [{$workflow->code}] must have at least 1 terminal step."
            );
        }

        // Check for orphan steps: steps that are not initial and have no incoming transitions
        $stepIds = $steps->pluck('id')->all();
        $reachableIds = $workflow->transitions()
            ->whereIn('to_step_id', $stepIds)
            ->pluck('to_step_id')
            ->unique()
            ->all();

        $initialId = $steps->firstWhere('is_initial', true)?->id;

        $orphans = $steps->filter(function ($step) use ($reachableIds, $initialId) {
            return $step->id !== $initialId && ! in_array($step->id, $reachableIds, false);
        });

        if ($orphans->isNotEmpty()) {
            $names = $orphans->pluck('name')->implode(', ');
            throw new WorkflowConfigurationException(
                "Workflow [{$workflow->code}] has unreachable steps: {$names}."
            );
        }
    }
}
