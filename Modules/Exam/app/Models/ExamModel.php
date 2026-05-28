<?php

namespace Modules\Exam\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Exam\Models\Concerns\HasExamUuid;

abstract class ExamModel extends Model
{
    use BelongsToTenant;
    use HasExamUuid;
}
