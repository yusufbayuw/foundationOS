<?php

namespace Tests\Regression;

use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('regression')]
class KnownBugRegistryTest extends TestCase
{
    public function test_every_registered_bug_has_a_regression_test_class(): void
    {
        foreach (KnownBugRegistry::BUGS as $bug) {
            $this->assertTrue(
                class_exists($bug['test_class']),
                "Regression test class missing for {$bug['id']}: {$bug['test_class']}",
            );
        }
    }

    public function test_registered_bug_ids_are_unique(): void
    {
        $ids = array_column(KnownBugRegistry::BUGS, 'id');

        $this->assertSame($ids, array_unique($ids));
    }
}
