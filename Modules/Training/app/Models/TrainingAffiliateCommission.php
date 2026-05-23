<?php

namespace Modules\Training\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Concerns\BelongsToTenant;

class TrainingAffiliateCommission extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'training_affiliate_id',
        'training_payment_id',
        'amount',
        'status',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }
}
