<?php

namespace Modules\InternalAudit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Monitoring\Models\Concerns\HasAuditTrail;
use Modules\Risk\Models\Risk;

class AuditFinding extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

    protected $table = 'audit_findings';

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'status',
        'description',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    protected static function booted(): void
    {
        static::created(function (AuditFinding $finding): void {
            if (($finding->meta['severity'] ?? null) !== 'high') {
                return;
            }

            Risk::withoutTenantScope()->firstOrCreate(
                [
                    'tenant_id' => $finding->tenant_id,
                    'code' => 'AUDIT-'.$finding->code,
                ],
                [
                    'organization_id' => $finding->organization_id,
                    'name' => $finding->name,
                    'status' => 'open',
                    'description' => $finding->description,
                    'riskable_type' => $finding->meta['auditee_type'] ?? null,
                    'riskable_id' => $finding->meta['auditee_id'] ?? null,
                    'likelihood' => 4,
                    'impact' => 5,
                    'score' => 20,
                    'residual_likelihood' => 3,
                    'residual_impact' => 4,
                    'residual_score' => 12,
                    'meta' => [
                        'source' => 'internal_audit',
                        'source_audit_finding_id' => $finding->id,
                        'category' => $finding->meta['category'] ?? null,
                    ],
                ],
            );
        });
    }
}
