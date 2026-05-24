<?php

return [
    'name' => 'School',
    'alias' => 'school',
    'domain' => 'academic_operations',
    'description' => 'School academic operations, student records, attendance, assessment, grades, and classroom workflows.',
    'tenant_scoped' => true,
    'depends_on' => [
        'Core',
    ],
];
