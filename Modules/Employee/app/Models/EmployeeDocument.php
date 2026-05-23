<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\User;

class EmployeeDocument extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'document_type',
        'document_number',
        'file_path',
        'original_filename',
        'issue_date',
        'expiry_date',
        'verification_status',
        'verified_at',
        'verified_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'expiry_date' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    public static function documentTypeOptions(): array
    {
        return [
            'ktp' => 'KTP',
            'npwp' => 'NPWP',
            'ijazah' => 'Ijazah',
            'kontrak' => 'Kontrak Kerja',
            'bpjs_kes' => 'BPJS Kesehatan',
            'bpjs_tk' => 'BPJS Ketenagakerjaan',
            'sk_pengangkatan' => 'SK Pengangkatan',
            'sertifikat' => 'Sertifikat / Lisensi',
            'lainnya' => 'Lainnya',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isExpired(): bool
    {
        return $this->expiry_date !== null && $this->expiry_date->isBefore(today());
    }
}
