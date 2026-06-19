<?php

namespace Modules\Dms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Dms\Database\Factories\DocumentFolderFactory;
use Modules\Monitoring\Models\Concerns\HasAuditTrail;

class DocumentFolder extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

    protected static function newFactory(): DocumentFolderFactory
    {
        return DocumentFolderFactory::new();
    }

    protected $table = 'document_folders';

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
}
