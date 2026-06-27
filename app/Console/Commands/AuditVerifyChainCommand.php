<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Monitoring\Models\AuditLog;

class AuditVerifyChainCommand extends Command
{
    protected $signature = 'audit:verify-chain {--tenant= : Limit verification to one tenant id}';

    protected $description = 'Verify the forensic audit log hash chain.';

    public function handle(): int
    {
        $query = AuditLog::withoutTenantScope()->orderBy('id');

        if ($this->option('tenant') !== null) {
            $query->where($query->getModel()->qualifyColumn('tenant_id'), (int) $this->option('tenant'));
        }

        $previousHash = null;

        foreach ($query->cursor() as $auditLog) {
            $expectedHash = AuditLog::calculateHash($previousHash, $auditLog->hashPayload());

            if ($auditLog->prev_hash !== $previousHash || $auditLog->current_hash !== $expectedHash) {
                $this->error("Audit hash chain mismatch at audit log {$auditLog->id}.");

                return self::FAILURE;
            }

            $previousHash = $auditLog->current_hash;
        }

        $this->info('Audit hash chain verified.');

        return self::SUCCESS;
    }
}
