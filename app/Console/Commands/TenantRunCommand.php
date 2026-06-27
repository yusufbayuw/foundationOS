<?php

namespace App\Console\Commands;

use App\Support\CurrentTenant;
use Illuminate\Console\Command;
use Modules\Core\Models\Tenant;

class TenantRunCommand extends Command
{
    protected $signature = 'tenant:run
        {tenant : Tenant ID or code}
        {cmd : Artisan command to run, e.g. "moodle:health-check"}
        {args?* : Additional arguments / options forwarded to the wrapped command}';

    protected $description = 'Run an artisan command inside the given tenant context (binds CurrentTenant for the duration of the call).';

    public function handle(CurrentTenant $currentTenant): int
    {
        $tenantArg = (string) $this->argument('tenant');

        $tenant = Tenant::query()
            ->when(ctype_digit($tenantArg), fn ($q) => $q->orWhere('id', (int) $tenantArg))
            ->orWhere('code', $tenantArg)
            ->orWhere('uuid', $tenantArg)
            ->first();

        if (! $tenant) {
            $this->error("Tenant not found: {$tenantArg}");

            return self::FAILURE;
        }

        $wrappedCommand = (string) $this->argument('cmd');
        $rawArgs = (array) ($this->argument('args'));
        $parameters = $this->parseForwardedArgs($rawArgs);

        $this->info("[tenant:run] tenant={$tenant->id} ({$tenant->code}) → {$wrappedCommand}");

        return (int) $currentTenant->forTenant($tenant, function () use ($wrappedCommand, $parameters) {
            return $this->call($wrappedCommand, $parameters);
        });
    }

    /**
     * Convert ["--flag=value", "--bare", "positional"] into the array shape
     * expected by Artisan::call().
     *
     * @param  array<int, string>  $args
     * @return array<int|string, string|bool>
     */
    protected function parseForwardedArgs(array $args): array
    {
        $parameters = [];
        $positional = 0;

        foreach ($args as $arg) {
            if (str_starts_with($arg, '--')) {
                $body = substr($arg, 2);
                if (str_contains($body, '=')) {
                    [$key, $value] = explode('=', $body, 2);
                    $parameters['--'.$key] = $value;
                } else {
                    $parameters['--'.$body] = true;
                }
            } else {
                $parameters[$positional++] = $arg;
            }
        }

        return $parameters;
    }
}
