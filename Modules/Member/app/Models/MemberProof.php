<?php

namespace Modules\Member\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'file_path',
        'disk',
        'mime_type',
        'status',
    ];

    protected $attributes = [
        'disk' => 'local',
        'status' => 'pending',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
