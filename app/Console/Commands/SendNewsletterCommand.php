<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Modules\Core\Models\Broadcast;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Core\Services\BroadcastService;

class SendNewsletterCommand extends Command
{
    protected $signature = 'comms:send-newsletter {--tenant=}';

    protected $description = 'Send scheduled newsletter broadcasts per tenant';

    public function handle(BroadcastService $broadcastService): int
    {
        $tenantOption = $this->option('tenant');
        $tenants = Tenant::query()
            ->when($tenantOption, fn ($q) => $q->whereKey($tenantOption))
            ->get();

        $sent = 0;

        foreach ($tenants as $tenant) {
            $broadcast = Broadcast::query()
                ->where('tenant_id', $tenant->getKey())
                ->where('status', 'draft')
                ->where('audience', 'newsletter')
                ->first();

            if (! $broadcast) {
                continue;
            }

            $users = User::query()
                ->whereHas('userTenantRoles', fn ($q) => $q->where('tenant_id', $tenant->getKey()))
                ->get();

            $broadcastService->send($broadcast, Collection::make($users));
            $sent++;
        }

        $this->info("Sent {$sent} newsletter broadcast(s).");

        return self::SUCCESS;
    }
}
