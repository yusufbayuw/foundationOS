<?php

namespace Modules\Core\Models;

use App\Models\Device;
use Database\Factories\UserFactory;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\Email\Concerns\InteractsWithEmailAuthentication;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Modules\Campus\Models\FeederLog;
use Modules\Campus\Models\Lecturer;
use Modules\Campus\Models\StudyPlan;
use Modules\Member\Models\Member;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;
use Modules\School\Models\Attendance;
use Modules\School\Models\Student;
use Modules\School\Models\StudentAchievement;
use Modules\School\Models\StudentAssessmentAnswer;
use Modules\School\Models\StudentGrade;
use Modules\School\Models\Teacher;
use Modules\School\Models\Violation;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasDefaultTenant, HasEmailAuthentication, HasTenants
{
    use HasApiTokens, HasFactory, HasRoles, InteractsWithAppAuthentication, InteractsWithAppAuthenticationRecovery, InteractsWithEmailAuthentication, LogsActivity, Notifiable, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'status', 'is_super_admin', 'preferred_locale'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'avatar',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'login_attempts',
        'locked_until',
        'timezone',
        'locale',
        'preferred_locale',
        'status',
        'pinned_menus',
        'app_authentication_secret',
        'app_authentication_recovery_codes',
        'has_email_authentication',
    ];

    protected $guarded = [
        'is_super_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function promoteToGlobalSuperAdmin(): static
    {
        $this->forceFill(['is_super_admin' => true])->save();

        return $this;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'login_attempts' => 'integer',
            'is_super_admin' => 'boolean',
            'password' => 'hashed',
            'pinned_menus' => 'array',
        ];
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function userTenantRoles(): HasMany
    {
        return $this->hasMany(UserTenantRole::class);
    }

    public function assignedTenantRoles(): HasMany
    {
        return $this->hasMany(UserTenantRole::class, 'assigned_by');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'user_tenant_roles')
            ->withPivot([
                'organization_id',
                'tenant_role_id',
                'assigned_by',
                'assigned_at',
                'expires_at',
                'is_primary',
            ])
            ->withTimestamps();
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'user_tenant_roles')
            ->withPivot([
                'tenant_id',
                'tenant_role_id',
                'assigned_by',
                'assigned_at',
                'expires_at',
                'is_primary',
            ])
            ->withTimestamps();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if (! $tenant instanceof Tenant) {
            return false;
        }

        return $this->activeUserTenantRolesQuery()
            ->where('tenant_id', $tenant->getKey())
            ->exists();
    }

    public function getTenants(Panel $panel): array|Collection
    {
        $tenantIds = $this->activeUserTenantRolesQuery()->pluck('tenant_id');

        return Tenant::query()
            ->whereIn('id', $tenantIds)
            ->orderBy('name')
            ->get();
    }

    /**
     * Membership rows that are not expired.
     */
    protected function activeUserTenantRolesQuery(): HasMany
    {
        return $this->userTenantRoles()
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        $primaryAssignment = $this->userTenantRoles()
            ->where('is_primary', true)
            ->with('tenant')
            ->first();

        if ($primaryAssignment?->tenant) {
            return $primaryAssignment->tenant;
        }

        return $this->getTenants($panel)->first();
    }

    public function createdTenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'created_by');
    }

    public function principalOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'principal_user_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function lecturers(): HasMany
    {
        return $this->hasMany(Lecturer::class);
    }

    public function verifiedAttendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'verified_by');
    }

    public function reportedViolations(): HasMany
    {
        return $this->hasMany(Violation::class, 'reported_by');
    }

    public function handledViolations(): HasMany
    {
        return $this->hasMany(Violation::class, 'handled_by');
    }

    public function gradedStudentAnswers(): HasMany
    {
        return $this->hasMany(StudentAssessmentAnswer::class, 'graded_by');
    }

    public function gradedStudentGrades(): HasMany
    {
        return $this->hasMany(StudentGrade::class, 'graded_by');
    }

    public function verifiedStudentAchievements(): HasMany
    {
        return $this->hasMany(StudentAchievement::class, 'verified_by');
    }

    public function approvedStudyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class, 'approved_by');
    }

    public function syncedFeederLogs(): HasMany
    {
        return $this->hasMany(FeederLog::class, 'synced_by');
    }

    public function processedSubscriptionLogs(): HasMany
    {
        return $this->hasMany(SubscriptionLog::class, 'processed_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function uploadedFiles(): HasMany
    {
        return $this->hasMany(FileUpload::class, 'uploaded_by');
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function isGlobalSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'platform') {
            setPermissionsTeamId(0);

            return $this->hasRole('platform_owner');
        }

        if ($panel->getId() === 'admin') {
            if ($this->isGlobalSuperAdmin()) {
                return true;
            }

            return $this->userTenantRoles()->exists();
        }

        if ($panel->getId() === 'parent') {
            return ParentStudent::query()->where('parent_user_id', $this->getKey())->exists();
        }

        return false;
    }
}
