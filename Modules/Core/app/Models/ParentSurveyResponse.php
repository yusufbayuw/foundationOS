<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentSurveyResponse extends Model
{
    protected $fillable = [
        'parent_survey_id',
        'parent_user_id',
        'answers',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
        ];
    }

    /**
     * @return BelongsTo<ParentSurvey, $this>
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(ParentSurvey::class, 'parent_survey_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function parentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }
}
