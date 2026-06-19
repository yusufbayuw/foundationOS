<?php

namespace Modules\Monitoring\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;

class FileUpload extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'uploaded_by',
        'organization_id',
        'fileable_type',
        'fileable_id',
        'collection_name',
        'disk',
        'directory',
        'original_name',
        'stored_name',
        'mime_type',
        'extension',
        'file_size',
        'checksum',
        'is_public',
        'metadata',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'is_public' => 'boolean',
            'metadata' => 'array',
            'uploaded_at' => 'datetime',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}
