<?php

use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\StudyProgram;
use Modules\Core\Models\Department;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;
use Modules\Employee\Models\Position;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Procurement\Models\ProcurementCategory;
use Modules\Procurement\Models\Vendor;

/**
 * Whitelist of Eloquent models that can be used as dynamic option sources
 * in workflow form schemas. Only models listed here can be queried via
 * DynamicOptionsResolver to prevent arbitrary model enumeration by tenants.
 *
 * Format: 'alias' => 'Fully\Qualified\Model\Class'
 * The alias is used in form_schema options_source.model field.
 */
return [
    'models' => [
        'Modules\\Core\\Models\\Organization' => Organization::class,
        'Modules\\Core\\Models\\Department' => Department::class,
        'Modules\\Core\\Models\\User' => User::class,
        'Modules\\Procurement\\Models\\Vendor' => Vendor::class,
        'Modules\\Procurement\\Models\\ProcurementCategory' => ProcurementCategory::class,
        'Modules\\Employee\\Models\\Position' => Position::class,
        'Modules\\Finance\\Models\\ChartOfAccount' => ChartOfAccount::class,
        'Modules\\Campus\\Models\\Faculty' => Faculty::class,
        'Modules\\Campus\\Models\\StudyProgram' => StudyProgram::class,
    ],

    'cache_ttl_seconds' => 300,

    'max_results' => 500,
];
