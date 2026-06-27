<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\PendingCommand;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function artisan($command, $parameters = []): PendingCommand
    {
        $result = parent::artisan($command, $parameters);

        if (! $result instanceof PendingCommand) {
            throw new \RuntimeException('Expected PendingCommand from artisan().');
        }

        return $result;
    }
}
