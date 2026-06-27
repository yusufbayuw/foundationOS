<?php

namespace Modules\Library\Support;

use App\Models\LibrarySlimsMapping;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Modules\Core\Models\OrganizationSetting;
use Modules\Core\Models\TenantSetting;

class SlimsImportService
{
    /**
     * @return array{connection: array<string, mixed>, email_domain: string, default_member_status: string}|null
     */
    public function resolveConfig(int $tenantId, ?int $organizationId = null): ?array
    {
        if ($organizationId !== null && $organizationId > 0) {
            $organizationSettings = $this->readOrganizationSettings($organizationId);

            if ($this->isEnabled($organizationSettings)) {
                return $this->normalizeSettings($organizationSettings);
            }
        }

        $tenantSettings = $this->readTenantSettings($tenantId);

        if (! $this->isEnabled($tenantSettings)) {
            return null;
        }

        return $this->normalizeSettings($tenantSettings);
    }

    /**
     * @return array<string, mixed>
     */
    protected function readTenantSettings(int $tenantId): array
    {
        $rows = TenantSetting::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('key', $this->settingKeys())
            ->get(['key', 'value']);

        return $this->mapSettings($rows->all());
    }

    /**
     * @return array<string, mixed>
     */
    protected function readOrganizationSettings(int $organizationId): array
    {
        $rows = OrganizationSetting::query()
            ->where('organization_id', $organizationId)
            ->whereIn('key', $this->settingKeys())
            ->get(['key', 'value']);

        return $this->mapSettings($rows->all());
    }

    /**
     * @return list<string>
     */
    protected function settingKeys(): array
    {
        return [
            'slims_import_enabled',
            'slims_db_host',
            'slims_db_port',
            'slims_db_database',
            'slims_db_username',
            'slims_db_password',
            'slims_db_charset',
            'slims_db_collation',
            'slims_db_prefix',
            'slims_import_email_domain',
            'slims_import_member_status',
        ];
    }

    /**
     * @param  array<int, OrganizationSetting|TenantSetting>  $rows
     * @return array<string, mixed>
     */
    protected function mapSettings(array $rows): array
    {
        $settings = [];

        foreach ($rows as $row) {
            $settings[$row->key] = is_string($row->value) ? trim($row->value) : $row->value;
        }

        return $settings;
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    protected function isEnabled(array $settings): bool
    {
        return filter_var($settings['slims_import_enabled'] ?? false, FILTER_VALIDATE_BOOL);
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array{connection: array<string, mixed>, email_domain: string, default_member_status: string}|null
     */
    protected function normalizeSettings(array $settings): ?array
    {
        $database = (string) ($settings['slims_db_database'] ?? '');
        $username = (string) ($settings['slims_db_username'] ?? '');

        if ($database === '' || $username === '') {
            return null;
        }

        return [
            'connection' => [
                'driver' => 'mysql',
                'host' => (string) ($settings['slims_db_host'] ?? '127.0.0.1'),
                'port' => is_numeric($settings['slims_db_port'] ?? null) ? (int) $settings['slims_db_port'] : 3306,
                'database' => $database,
                'username' => $username,
                'password' => (string) ($settings['slims_db_password'] ?? ''),
                'charset' => (string) ($settings['slims_db_charset'] ?? 'utf8mb4'),
                'collation' => (string) ($settings['slims_db_collation'] ?? 'utf8mb4_unicode_ci'),
                'prefix' => (string) ($settings['slims_db_prefix'] ?? ''),
                'strict' => false,
            ],
            'email_domain' => (string) ($settings['slims_import_email_domain'] ?? 'slims.local'),
            'default_member_status' => (string) ($settings['slims_import_member_status'] ?? 'active'),
        ];
    }

    public function hasMapping(string $entityType, int $tenantId, string $slimsId): bool
    {
        return LibrarySlimsMapping::query()
            ->where('tenant_id', $tenantId)
            ->where('entity_type', $entityType)
            ->where('slims_id', $slimsId)
            ->exists();
    }

    /**
     * Apply an incremental filter only when the legacy table exposes a compatible datetime/date column.
     *
     * @param  list<string>  $columns
     */
    public function applySince(Builder $query, ConnectionInterface $connection, string $table, ?string $since, array $columns): Builder
    {
        if ($since === null || trim($since) === '') {
            return $query;
        }

        foreach ($columns as $column) {
            if ($this->hasColumn($connection, $table, $column)) {
                return $query->where($column, '>=', $since);
            }
        }

        return $query;
    }

    protected function hasColumn(ConnectionInterface $connection, string $table, string $column): bool
    {
        if (! $connection instanceof Connection) {
            return false;
        }

        try {
            return $connection->getSchemaBuilder()->hasColumn($table, $column);
        } catch (\Throwable) {
            return false;
        }
    }
}
