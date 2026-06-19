<?php

namespace Modules\Library\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibrarySerialIssue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'serial_id',
        'expected_date',
        'received_date',
        'sequence_number',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'expected_date' => 'date',
            'received_date' => 'date',
        ];
    }

    public function serial(): BelongsTo
    {
        return $this->belongsTo(LibrarySerial::class, 'serial_id');
    }
}
