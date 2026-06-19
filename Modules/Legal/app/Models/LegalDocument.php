<?php

namespace Modules\Legal\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Legal\Database\Factories\LegalDocumentFactory;
use Modules\Monitoring\Models\Concerns\HasAuditTrail;

class LegalDocument extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

    protected static function newFactory(): LegalDocumentFactory
    {
        return LegalDocumentFactory::new();
    }

    protected $table = 'legal_documents';

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'document_type',
        'effective_date',
        'expires_at',
        'file_path',
        'status',
        'description',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'effective_date' => 'date',
            'expires_at' => 'date',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
