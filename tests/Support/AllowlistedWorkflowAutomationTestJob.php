<?php

namespace Tests\Support;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AllowlistedWorkflowAutomationTestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var list<array{instance_id: int, payload: array<string, mixed>}> */
    public static array $dispatched = [];

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public int $instanceId,
        public array $payload = [],
    ) {}

    public function handle(): void
    {
        self::$dispatched[] = [
            'instance_id' => $this->instanceId,
            'payload' => $this->payload,
        ];
    }

    public static function reset(): void
    {
        self::$dispatched = [];
    }
}
