<?php

namespace Modules\Training\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Concerns\BelongsToTenant;

class TrainingCertificate extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'training_enrollment_id',
        'certificate_number',
        'verification_token',
        'issued_at',
    ];

    protected function casts(): array
    {
        return ['issued_at' => 'datetime'];
    }

    /**
     * @return BelongsTo<TrainingEnrollment, $this>
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(TrainingEnrollment::class, 'training_enrollment_id');
    }

    public function isPrintable(): bool
    {
        return $this->issued_at !== null;
    }
}
