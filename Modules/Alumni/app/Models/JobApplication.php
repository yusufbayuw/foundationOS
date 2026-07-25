<?php

namespace Modules\Alumni\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\User;

class JobApplication extends Model
{
    use BelongsToTenant, HasFactory;

    protected $table = 'job_applications';

    protected $fillable = [
        'tenant_id',
        'job_posting_id',
        'user_id',
        'status',
        'cover_letter',
        'resume_path',
        'meta',
    ];

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
