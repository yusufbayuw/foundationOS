<?php

namespace Modules\Workflow\Services;

use Illuminate\Support\Collection;
use Modules\Core\Models\User;
use Modules\Workflow\Contracts\RuleEngine;
use Modules\Workflow\Contracts\WorkflowDynamicAssigneeResolver;
use Modules\Workflow\Contracts\WorkflowAssigneeResolver;
use Modules\Workflow\Enums\WorkflowAssigneeType;
use Modules\Workflow\Exceptions\WorkflowConfigurationException;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Support\WorkflowContextData;

class DatabaseWorkflowAssigneeResolver implements WorkflowAssigneeResolver
{
    public function __construct(private readonly RuleEngine $ruleEngine) {}

    public function resolveUsers(WorkflowInstance $instance, WorkflowStep $step): Collection
    {
        $type = $step->assignee_type;

        return match ($type) {
            WorkflowAssigneeType::User => $this->resolveDirectUser($step),
            WorkflowAssigneeType::Role => $this->resolveRoleUsers($instance, $step),
            WorkflowAssigneeType::SubjectField => $this->resolveSubjectFieldUsers($instance, $step),
            WorkflowAssigneeType::RequesterManager => $this->resolveRequesterManager($instance, $step),
            WorkflowAssigneeType::Resolver => $this->resolveCustomResolver($instance, $step),
            null => collect(),
        };
    }

    protected function resolveDirectUser(WorkflowStep $step): Collection
    {
        $userId = (int) $step->assignee_value;
        $user = User::query()->find($userId);

        return $user ? collect([$user]) : collect();
    }

    protected function resolveRoleUsers(WorkflowInstance $instance, WorkflowStep $step): Collection
    {
        $candidates = collect(data_get($step->assignee_config, 'candidates', []));

        if ($candidates->isNotEmpty()) {
            $matched = $this->ruleEngine->resolveCandidates($candidates, WorkflowContextData::fromInstance($instance));
            $role = (string) data_get($matched->first(), 'role', $step->assignee_value);
        } else {
            $role = (string) $step->assignee_value;
        }

        return User::query()
            ->role($role)
            ->whereHas('userTenantRoles', function ($query) use ($instance): void {
                $query->where('tenant_id', $instance->tenant_id);

                if ($instance->organization_id) {
                    $query->where(function ($inner) use ($instance): void {
                        $inner->where('organization_id', $instance->organization_id)
                            ->orWhereNull('organization_id');
                    });
                }
            })
            ->get();
    }

    protected function resolveSubjectFieldUsers(WorkflowInstance $instance, WorkflowStep $step): Collection
    {
        $field = (string) $step->assignee_value;
        $userId = data_get($instance->context_data ?? [], $field)
            ?? data_get($instance->form_data ?? [], $field)
            ?? data_get($instance->computed_data ?? [], $field);

        if (! $userId && $instance->subject) {
            $userId = data_get($instance->subject, $field);
        }

        if (! $userId) {
            return collect();
        }

        $user = User::query()->find((int) $userId);

        return $user ? collect([$user]) : collect();
    }

    protected function resolveRequesterManager(WorkflowInstance $instance, WorkflowStep $step): Collection
    {
        $candidates = array_filter([
            data_get($step->assignee_config, 'manager_field'),
            'requester_manager_id',
            'manager_id',
            'supervisor_id',
            'approver_id',
        ]);

        foreach ($candidates as $field) {
            $userId = data_get(WorkflowContextData::fromInstance($instance), $field);

            if ($userId && ($user = User::query()->find((int) $userId))) {
                return collect([$user]);
            }
        }

        throw new WorkflowConfigurationException('Requester manager assignee requires a manager user id in workflow context.');
    }

    protected function resolveCustomResolver(WorkflowInstance $instance, WorkflowStep $step): Collection
    {
        $resolverClass = (string) (data_get($step->assignee_config, 'resolver_class') ?: $step->assignee_value);

        if ($resolverClass === '') {
            throw new WorkflowConfigurationException('Custom assignee resolver class is missing.');
        }

        $allowedResolvers = (array) config('workflow.allowed_assignee_resolvers', []);

        if (! in_array($resolverClass, $allowedResolvers, true)) {
            throw new WorkflowConfigurationException("Custom assignee resolver [{$resolverClass}] is not allowed.");
        }

        $resolver = app($resolverClass);

        if (! $resolver instanceof WorkflowDynamicAssigneeResolver) {
            throw new WorkflowConfigurationException("Custom assignee resolver [{$resolverClass}] must implement WorkflowDynamicAssigneeResolver.");
        }

        return $resolver->resolve($instance, $step);
    }
}
