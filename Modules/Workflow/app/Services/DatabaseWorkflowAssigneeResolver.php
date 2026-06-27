<?php

namespace Modules\Workflow\Services;

use App\Support\TypedValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Workflow\Contracts\RuleEngine;
use Modules\Workflow\Contracts\WorkflowAssigneeResolver;
use Modules\Workflow\Contracts\WorkflowDynamicAssigneeResolver;
use Modules\Workflow\Enums\WorkflowAssigneeType;
use Modules\Workflow\Exceptions\WorkflowConfigurationException;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Support\WorkflowContextData;

class DatabaseWorkflowAssigneeResolver implements WorkflowAssigneeResolver
{
    public function __construct(private readonly RuleEngine $ruleEngine) {}

    /**
     * @return Collection<int, User>
     */
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

    /**
     * @return Collection<int, User>
     */
    protected function resolveDirectUser(WorkflowStep $step): Collection
    {
        $userId = TypedValue::int($step->assignee_value);
        $user = User::query()->find($userId);

        return $user instanceof User ? collect([$user]) : collect();
    }

    /**
     * @return Collection<int, User>
     */
    protected function resolveRoleUsers(WorkflowInstance $instance, WorkflowStep $step): Collection
    {
        /** @var list<array<string, mixed>> $candidateConfig */
        $candidateConfig = data_get($step->assignee_config, 'candidates', []);
        $candidates = collect($candidateConfig);

        if ($candidates->isNotEmpty()) {
            $matched = $this->ruleEngine->resolveCandidates($candidates, WorkflowContextData::fromInstance($instance));
            if ($matched instanceof Collection && $matched->isNotEmpty()) {
                $first = $matched->first();
                $role = TypedValue::string(is_array($first) ? data_get($first, 'role') : null, TypedValue::string($step->assignee_value));
            } else {
                $role = TypedValue::string($step->assignee_value);
            }
        } else {
            $role = TypedValue::string($step->assignee_value);
        }

        return User::query()
            ->role($role)
            ->whereHas('userTenantRoles', function (Builder $query) use ($instance): void {
                /** @var Builder<UserTenantRole> $query */
                $query->where('tenant_id', $instance->tenant_id);

                if ($instance->organization_id) {
                    $query->where(function (Builder $inner) use ($instance): void {
                        $inner->where('organization_id', $instance->organization_id)
                            ->orWhereNull('organization_id');
                    });
                }
            })
            ->get();
    }

    /**
     * @return Collection<int, User>
     */
    protected function resolveSubjectFieldUsers(WorkflowInstance $instance, WorkflowStep $step): Collection
    {
        $field = TypedValue::string($step->assignee_value);
        $userId = data_get($instance->context_data ?? [], $field)
            ?? data_get($instance->form_data ?? [], $field)
            ?? data_get($instance->computed_data ?? [], $field);

        if (! $userId && $instance->subject) {
            $userId = data_get($instance->subject, $field);
        }

        if (! $userId) {
            return collect();
        }

        $user = User::query()->find(TypedValue::int($userId));

        return $user instanceof User ? collect([$user]) : collect();
    }

    /**
     * @return Collection<int, User>
     */
    protected function resolveRequesterManager(WorkflowInstance $instance, WorkflowStep $step): Collection
    {
        /** @var list<string|null> $candidateFields */
        $candidateFields = array_values(array_filter([
            data_get($step->assignee_config, 'manager_field'),
            'requester_manager_id',
            'manager_id',
            'supervisor_id',
            'approver_id',
        ], fn (mixed $field): bool => is_string($field) && $field !== ''));

        $context = WorkflowContextData::fromInstance($instance);

        foreach ($candidateFields as $field) {
            $userId = data_get($context, $field);

            if ($userId !== null && $userId !== '') {
                $user = User::query()->find(TypedValue::int($userId));

                if ($user instanceof User) {
                    return collect([$user]);
                }
            }
        }

        throw new WorkflowConfigurationException('Requester manager assignee requires a manager user id in workflow context.');
    }

    /**
     * @return Collection<int, User>
     */
    protected function resolveCustomResolver(WorkflowInstance $instance, WorkflowStep $step): Collection
    {
        $resolverClass = TypedValue::string(
            data_get($step->assignee_config, 'resolver_class') ?: $step->assignee_value,
        );

        if ($resolverClass === '') {
            throw new WorkflowConfigurationException('Custom assignee resolver class is missing.');
        }

        /** @var list<class-string> $allowedResolvers */
        $allowedResolvers = config('workflow.allowed_assignee_resolvers', []);

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
