<?php

namespace Modules\Workflow\Services;

use Modules\Workflow\Enums\WorkflowGatewayType;
use Modules\Workflow\Enums\WorkflowQuorumStrategy;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;

/**
 * Coordinates parallel approval / quorum decisions at a single workflow step.
 *
 * V2 paths (linear, single-assignee) are unaffected: only steps that declare
 * a quorum_strategy or a non-`none` gateway_type opt into coordinator logic.
 *
 * The "branches" interpretation here is per-assignee at the same step
 * (e.g. 3 paralel managers, quorum=majority). Cross-step branch tracking
 * via workflow_step_branches is left as a separate join-point primitive.
 */
class WorkflowParallelCoordinator
{
    public const OUTCOME_PENDING = 'pending';

    public const OUTCOME_APPROVE = 'approve';

    public const OUTCOME_REJECT = 'reject';

    /**
     * True when the step requires per-assignee outcome aggregation instead of
     * the legacy "one approver advances all" behavior.
     */
    public function isParallel(WorkflowStep $step): bool
    {
        $gateway = $step->gateway_type;
        $quorum = $step->quorum_strategy;

        $gatewayIsParallel = $gateway instanceof WorkflowGatewayType
            && $gateway !== WorkflowGatewayType::None;

        return $gatewayIsParallel || $quorum instanceof WorkflowQuorumStrategy;
    }

    /**
     * Evaluate whether quorum has been reached at the given step and what the
     * aggregate outcome is.
     *
     * Returns:
     *   [
     *     'reached' => bool,
     *     'outcome' => 'approve'|'reject'|'pending',
     *     'approves' => int,
     *     'rejects' => int,
     *     'total'   => int,
     *     'decided' => int,
     *   ]
     *
     * @return array{reached:bool,outcome:string,approves:int,rejects:int,total:int,decided:int}
     */
    public function evaluate(WorkflowInstance $instance, WorkflowStep $step): array
    {
        $assignments = $instance->assignments()
            ->where('step_id', $step->getKey())
            ->get(['status', 'outcome']);

        $total = $assignments->count();
        $approves = $assignments->where('outcome', self::OUTCOME_APPROVE)->count();
        $rejects = $assignments->where('outcome', self::OUTCOME_REJECT)->count();
        $decided = $approves + $rejects;

        $strategy = $step->quorum_strategy ?? WorkflowQuorumStrategy::All;
        $threshold = $this->thresholdFor($strategy, $step->quorum_value, $total);

        $outcome = self::OUTCOME_PENDING;
        $reached = false;

        if ($approves >= $threshold) {
            $outcome = self::OUTCOME_APPROVE;
            $reached = true;
        } elseif ($this->rejectsBlockQuorum($rejects, $total, $threshold)) {
            // Enough rejections that approval threshold can no longer be reached.
            $outcome = self::OUTCOME_REJECT;
            $reached = true;
        } elseif ($strategy === WorkflowQuorumStrategy::All && $decided >= $total && $total > 0) {
            // Strategy=all: everybody voted but threshold not met → reject.
            $outcome = self::OUTCOME_REJECT;
            $reached = true;
        }

        return [
            'reached' => $reached,
            'outcome' => $outcome,
            'approves' => $approves,
            'rejects' => $rejects,
            'total' => $total,
            'decided' => $decided,
            'threshold' => $threshold,
        ];
    }

    /**
     * Number of approves required to satisfy the quorum strategy.
     */
    protected function thresholdFor(WorkflowQuorumStrategy $strategy, ?int $value, int $total): int
    {
        return match ($strategy) {
            WorkflowQuorumStrategy::All => max(1, $total),
            WorkflowQuorumStrategy::Majority => intdiv($total, 2) + 1,
            WorkflowQuorumStrategy::Count => max(1, (int) ($value ?? 1)),
            WorkflowQuorumStrategy::Percentage => max(
                1,
                (int) ceil(($value ?? 0) / 100 * $total),
            ),
        };
    }

    /**
     * True when remaining undecided approvers cannot push approves to the threshold.
     */
    protected function rejectsBlockQuorum(int $rejects, int $total, int $threshold): bool
    {
        $remaining = $total - $rejects;

        return $remaining < $threshold;
    }
}
