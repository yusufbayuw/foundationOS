<?php

namespace Modules\Exam\Services;

use App\Models\Role;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamPermission;
use Modules\Exam\Enums\ExamRole;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class ExamShieldProvisioner
{
    protected function guardName(): string
    {
        return (string) config('auth.defaults.guard', 'web');
    }

    /**
     * @var list<string>
     */
    protected array $resourcePermissions = [
        'ViewAny:ExamDefinition',
        'View:ExamDefinition',
        'Create:ExamDefinition',
        'Update:ExamDefinition',
        'Delete:ExamDefinition',
        'DeleteAny:ExamDefinition',
        'Restore:ExamDefinition',
        'ForceDelete:ExamDefinition',
        'Replicate:ExamDefinition',
        'Reorder:ExamDefinition',
        'ViewAny:ExamParticipant',
        'View:ExamParticipant',
        'Create:ExamParticipant',
        'Update:ExamParticipant',
        'Delete:ExamParticipant',
        'DeleteAny:ExamParticipant',
        'Restore:ExamParticipant',
        'ForceDelete:ExamParticipant',
        'ViewAny:ExamQuestionBank',
        'View:ExamQuestionBank',
        'Create:ExamQuestionBank',
        'Update:ExamQuestionBank',
        'Delete:ExamQuestionBank',
        'DeleteAny:ExamQuestionBank',
        'Restore:ExamQuestionBank',
        'ForceDelete:ExamQuestionBank',
        'ViewAny:ExamQuestion',
        'View:ExamQuestion',
        'Create:ExamQuestion',
        'Update:ExamQuestion',
        'Delete:ExamQuestion',
        'DeleteAny:ExamQuestion',
        'Restore:ExamQuestion',
        'ForceDelete:ExamQuestion',
        'ViewAny:ExamToken',
        'View:ExamToken',
        'Create:ExamToken',
        'Update:ExamToken',
        'Delete:ExamToken',
        'DeleteAny:ExamToken',
        'Restore:ExamToken',
        'ForceDelete:ExamToken',
    ];

    public function provisionForTenant(Tenant $tenant): void
    {
        setPermissionsTeamId($tenant->getKey());

        $allPermissions = array_merge(ExamPermission::values(), $this->resourcePermissions);

        $guard = $this->guardName();

        foreach ($allPermissions as $permissionName) {
            Permission::findOrCreate($permissionName, $guard);
        }

        $this->syncRole(ExamRole::ExamAdmin->value, $allPermissions, $tenant);
        $this->syncRole(ExamRole::Teacher->value, $this->teacherPermissions(), $tenant);
        $this->syncRole(ExamRole::Lecturer->value, $this->lecturerPermissions(), $tenant);
        $this->syncRole(ExamRole::Proctor->value, $this->proctorPermissions(), $tenant);
        $this->syncRole(ExamRole::Viewer->value, $this->viewerPermissions(), $tenant);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @param  list<string>  $permissions
     */
    protected function syncRole(string $roleName, array $permissions, Tenant $tenant): void
    {
        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => $this->guardName(),
            'tenant_id' => $tenant->getKey(),
        ]);

        $role->syncPermissions($permissions);
    }

    /**
     * @return list<string>
     */
    protected function teacherPermissions(): array
    {
        return [
            ExamPermission::ViewExam->value,
            ExamPermission::CreateExam->value,
            ExamPermission::UpdateExam->value,
            ExamPermission::GradeExamAnswer->value,
            ExamPermission::ExportExamResult->value,
            ExamPermission::PushExamGradebook->value,
            ExamPermission::ManageQuestionBank->value,
            ExamPermission::ImportQuestion->value,
            ExamPermission::ManageExamToken->value,
            'ViewAny:ExamDefinition',
            'View:ExamDefinition',
            'Create:ExamDefinition',
            'Update:ExamDefinition',
            'Replicate:ExamDefinition',
            'ViewAny:ExamParticipant',
            'View:ExamParticipant',
            'Create:ExamParticipant',
            'Update:ExamParticipant',
            'ViewAny:ExamQuestionBank',
            'View:ExamQuestionBank',
            'Create:ExamQuestionBank',
            'Update:ExamQuestionBank',
            'ViewAny:ExamQuestion',
            'View:ExamQuestion',
            'Create:ExamQuestion',
            'Update:ExamQuestion',
        ];
    }

    /**
     * @return list<string>
     */
    protected function lecturerPermissions(): array
    {
        return $this->teacherPermissions();
    }

    /**
     * @return list<string>
     */
    protected function proctorPermissions(): array
    {
        return [
            ExamPermission::ViewExam->value,
            ExamPermission::ManageExamProctor->value,
            ExamPermission::OpenControlRoom->value,
            ExamPermission::SyncExamResult->value,
            ExamPermission::PushExamGradebook->value,
            'ViewAny:ExamDefinition',
            'View:ExamDefinition',
            'ViewAny:ExamParticipant',
            'View:ExamParticipant',
        ];
    }

    /**
     * @return list<string>
     */
    protected function viewerPermissions(): array
    {
        return [
            ExamPermission::ViewExam->value,
            ExamPermission::ExportExamResult->value,
            'ViewAny:ExamDefinition',
            'View:ExamDefinition',
            'ViewAny:ExamParticipant',
            'View:ExamParticipant',
        ];
    }
}
