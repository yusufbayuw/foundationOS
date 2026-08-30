<?php

namespace Modules\Alumni\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\User;
use Spatie\Activitylog\Facades\Activity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class JobApplication extends Model
{
    use BelongsToTenant, HasFactory, LogsActivity;

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_IN_REVIEW = 'in_review';

    public const STATUS_SHORTLISTED = 'shortlisted';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_REJECTED = 'rejected';

    /**
     * @var array<string, list<string>>
     */
    private const STATUS_TRANSITIONS = [
        self::STATUS_SUBMITTED => [self::STATUS_IN_REVIEW, self::STATUS_REJECTED],
        self::STATUS_IN_REVIEW => [self::STATUS_SHORTLISTED, self::STATUS_ACCEPTED, self::STATUS_REJECTED],
        self::STATUS_SHORTLISTED => [self::STATUS_IN_REVIEW, self::STATUS_ACCEPTED, self::STATUS_REJECTED],
        self::STATUS_ACCEPTED => [self::STATUS_IN_REVIEW],
        self::STATUS_REJECTED => [self::STATUS_IN_REVIEW],
    ];

    protected $table = 'job_applications';

    protected $fillable = [
        'tenant_id',
        'job_posting_id',
        'user_id',
        'cover_letter',
        'resume_path',
        'meta',
    ];

    protected $attributes = [
        'status' => self::STATUS_SUBMITTED,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_IN_REVIEW => 'In review',
            self::STATUS_SHORTLISTED => 'Shortlisted',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function reviewStatusOptions(): array
    {
        $currentStatus = $this->currentStatus();
        $availableStatuses = [
            $currentStatus,
            ...(self::STATUS_TRANSITIONS[$currentStatus] ?? []),
        ];

        return array_intersect_key(self::statusOptions(), array_flip($availableStatuses));
    }

    public function canTransitionTo(string $status): bool
    {
        $currentStatus = $this->currentStatus();

        return $status === $currentStatus
            || in_array($status, self::STATUS_TRANSITIONS[$currentStatus] ?? [], true);
    }

    public function transitionStatus(string $status, User $reviewer): void
    {
        if (! $this->canTransitionTo($status)) {
            throw new DomainException("The application cannot transition from {$this->currentStatus()} to {$status}.");
        }

        if ($status === $this->currentStatus()) {
            return;
        }

        Activity::defaultCauser($reviewer, function () use ($status): void {
            $this->forceFill(['status' => $status])->save();
        });
    }

    private function currentStatus(): string
    {
        return (string) $this->getAttribute('status');
    }

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
