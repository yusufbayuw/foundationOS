<?php

namespace Tests\Regression;

/**
 * Catalog of known bugs that must remain covered by regression tests.
 *
 * When fixing a production bug, add a test class under tests/Regression/
 * and register the bug id here so CI can verify coverage exists.
 */
final class KnownBugRegistry
{
    /**
     * @var list<array{id: string, test_class: class-string, description: string}>
     */
    public const BUGS = [
        [
            'id' => 'BUG-2026-001',
            'test_class' => MoodleOutboxTenantForeignKeyRegressionTest::class,
            'description' => 'Moodle outbox inserts must reference an existing tenant_id.',
        ],
        [
            'id' => 'BUG-2026-002',
            'test_class' => JsonLogicDepthRegressionTest::class,
            'description' => 'JSON Logic evaluation must cap recursion depth.',
        ],
        [
            'id' => 'BUG-2026-003',
            'test_class' => WorkflowCanvasEagerLoadRegressionTest::class,
            'description' => 'WorkflowCanvas must eager-load steps and transitions safely.',
        ],
    ];
}
