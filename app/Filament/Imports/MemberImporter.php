<?php

namespace App\Filament\Imports;

use Modules\Library\Models\Member;

class MemberImporter extends BaseModelImporter
{
    protected static ?string $model = Member::class;
}
