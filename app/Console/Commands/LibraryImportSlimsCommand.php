<?php

namespace App\Console\Commands;

use App\Models\LibrarySlimsMapping;
use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Library\Models\Book;
use Modules\Library\Models\BookCopy;
use Modules\Library\Models\Loan;
use Modules\Library\Models\Member;
use Modules\Library\Support\SlimsImportService;

class LibraryImportSlimsCommand extends Command
{
    protected string $emailDomain = 'slims.local';
    protected string $defaultMemberStatus = 'active';
    protected bool $skipExisting = false;
    protected ?string $since = null;
    protected SlimsImportService $slimsImportService;

    protected $signature = 'fos:library:import-slims
        {--tenant= : Target tenant_id in FOS}
        {--organization= : Optional target organization_id in FOS (kosong = tenant-wide)}
        {--tenant-role-id= : Role id used for imported member user assignment}
        {--entity=all : catalog|members|loans|all}
        {--limit=0 : Limit rows per entity (0 = no limit)}
        {--upsert : Explicitly allow update/create behavior for mapped records}
        {--skip-existing : Only import records that have not been mapped yet}
        {--since= : Incremental import from the given date/datetime when SLiMS table supports it}
        {--dry-run : Simulate import without writing}';

    protected $description = 'Import legacy SLiMS data into FOS Library per-tenant with idempotent mapping';

    public function handle(SlimsImportService $slimsImportService): int
    {
        $this->slimsImportService = $slimsImportService;
        $tenantId = is_numeric($this->option('tenant')) ? (int) $this->option('tenant') : 0;
        $organizationId = is_numeric($this->option('organization')) ? (int) $this->option('organization') : null;
        $tenantRoleId = is_numeric($this->option('tenant-role-id')) ? (int) $this->option('tenant-role-id') : null;
        $entity = strtolower((string) $this->option('entity'));
        $limit = max(0, (int) $this->option('limit'));
        $dryRun = (bool) $this->option('dry-run');
        $this->skipExisting = (bool) $this->option('skip-existing');
        $this->since = ($since = trim((string) $this->option('since'))) !== '' ? $since : null;

        if (! in_array($entity, ['catalog', 'members', 'loans', 'all'], true)) {
            $this->error('Entity harus salah satu dari: catalog, members, loans, all.');
            return self::FAILURE;
        }

        if ($tenantId <= 0) {
            $this->error('Opsi --tenant wajib diisi dengan ID numerik valid.');
            return self::FAILURE;
        }

        $tenant = Tenant::query()->find($tenantId);
        if (! $tenant) {
            $this->error('Tenant tidak ditemukan.');
            return self::FAILURE;
        }

        if ($organizationId !== null) {
            $organization = Organization::query()->where('tenant_id', $tenantId)->find($organizationId);
            if (! $organization) {
                $this->error('Organization tidak ditemukan atau bukan milik tenant tersebut.');
                return self::FAILURE;
            }
        }

        $memberRole = $this->resolveMemberRole($tenantId, $tenantRoleId);
        if (! $memberRole) {
            $this->error('Tenant role untuk member tidak ditemukan. Berikan --tenant-role-id yang valid.');
            return self::FAILURE;
        }

        $tenantSlimsConfig = $this->resolveTenantSlimsConfig($tenantId, $organizationId);
        if ($tenantSlimsConfig === null) {
            $this->error($organizationId !== null
                ? 'Konfigurasi SLiMS organization tidak lengkap atau belum diaktifkan di organization_settings. Fallback tenant dipakai hanya untuk mode tenant-wide.'
                : 'Konfigurasi SLiMS tenant tidak lengkap atau belum diaktifkan di tenant_settings.');
            return self::FAILURE;
        }

        try {
            $connection = $this->slimsConnection($tenantSlimsConfig['connection']);
        } catch (\RuntimeException $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        if (! $this->validateSlimsSchema($connection)) {
            $this->error('Koneksi SLiMS validasi gagal. Pastikan DB SLiMS terisi dan tabel inti tersedia.');
            return self::FAILURE;
        }

        $summary = [
            'books' => 0,
            'copies' => 0,
            'members' => 0,
            'users' => 0,
            'loans' => 0,
            'skipped' => 0,
        ];

        if (in_array($entity, ['catalog', 'all'], true)) {
            $this->importCatalog($connection, $tenantId, $organizationId, $limit, $dryRun, $summary);
        }

        if (in_array($entity, ['members', 'all'], true)) {
            $this->importMembers($connection, $tenantId, $organizationId, $memberRole->id, $limit, $dryRun, $summary);
        }

        if (in_array($entity, ['loans', 'all'], true)) {
            $this->importLoans($connection, $tenantId, $organizationId, $limit, $dryRun, $summary);
        }

        $this->table(
            ['Metric', 'Value'],
            [
                ['books', (string) $summary['books']],
                ['copies', (string) $summary['copies']],
                ['members', (string) $summary['members']],
                ['users', (string) $summary['users']],
                ['loans', (string) $summary['loans']],
                ['skipped', (string) $summary['skipped']],
            ],
        );

        $this->info($dryRun
            ? 'Dry-run import selesai. Tidak ada data FOS yang ditulis.'
            : 'Import SLiMS selesai. Jalankan ulang command ini aman (idempotent per tenant).');

        return self::SUCCESS;
    }

    protected function importCatalog(
        ConnectionInterface $connection,
        int $tenantId,
        ?int $organizationId,
        int $limit,
        bool $dryRun,
        array &$summary,
    ): void {
        $bookQuery = $connection->table('biblio')->orderBy('biblio_id');
        $bookQuery = $this->slimsImportService->applySince($bookQuery, $connection, 'biblio', $this->since, ['last_update', 'input_date']);
        if ($limit > 0) {
            $bookQuery->limit($limit);
        }

        $bookRows = $bookQuery->get();
        foreach ($bookRows as $row) {
            $slimsId = (string) $row->biblio_id;
            if ($this->shouldSkipEntity('book', $tenantId, $slimsId)) {
                $summary['skipped']++;
                continue;
            }
            $book = $this->findOrMakeMappedModel('book', $tenantId, $slimsId, Book::class);

            if (! $book) {
                $summary['skipped']++;
                continue;
            }

            $book->fill([
                'tenant_id' => $tenantId,
                'organization_id' => $organizationId,
                'book_category_id' => null,
                'isbn' => $row->isbn_issn ?: null,
                'isbn13' => null,
                'title' => (string) ($row->title ?: "Untitled {$row->biblio_id}"),
                'subtitle' => null,
                'authors' => $this->parseAuthors((string) ($row->sor ?? '')),
                'publisher' => null,
                'publication_year' => $row->publish_year ?: null,
                'publication_place' => null,
                'edition' => $row->edition ?: null,
                'volume' => null,
                'series' => $row->series_title ?: null,
                'language' => $this->mapLanguage((string) ($row->language_id ?? 'id')),
                'pages' => null,
                'dimensions' => null,
                'weight_grams' => null,
                'binding_type' => null,
                'classification_code' => $row->classification ?: null,
                'keywords' => null,
                'synopsis' => $row->notes ?: null,
                'cover_image' => $row->image ?: null,
                'preview_url' => null,
                'purchase_price' => null,
                'source' => 'slims_import',
                'total_copies' => (int) ($book->total_copies ?? 0),
                'available_copies' => (int) ($book->available_copies ?? 0),
                'location_shelf' => null,
                'is_active' => true,
                'is_reference_only' => false,
            ]);

            if (! $dryRun) {
                $book->save();
                $this->upsertMapping('book', $tenantId, $slimsId, (int) $book->id, [
                    'title' => (string) $book->title,
                ]);
            }

            $summary['books']++;
        }

        $copyQuery = $connection->table('item')->orderBy('item_id');
        $copyQuery = $this->slimsImportService->applySince($copyQuery, $connection, 'item', $this->since, ['last_update', 'input_date', 'received_date']);
        if ($limit > 0) {
            $copyQuery->limit($limit);
        }

        $copyRows = $copyQuery->get();
        $touchedBookIds = [];

        foreach ($copyRows as $row) {
            if ($this->shouldSkipEntity('copy', $tenantId, (string) $row->item_id)) {
                $summary['skipped']++;
                continue;
            }

            $bookMapping = $this->findMapping('book', $tenantId, (string) $row->biblio_id);
            if (! $bookMapping) {
                $summary['skipped']++;
                continue;
            }

            $copy = $this->findOrMakeMappedModel('copy', $tenantId, (string) $row->item_id, BookCopy::class);
            if (! $copy) {
                $summary['skipped']++;
                continue;
            }

            $copyNumber = (string) ($row->item_code ?: "copy-{$row->item_id}");
            $copy->fill([
                'tenant_id' => $tenantId,
                'organization_id' => $organizationId,
                'book_id' => (int) $bookMapping->fos_id,
                'copy_number' => $copyNumber,
                'barcode' => $row->item_code ?: null,
                'acquisition_date' => $row->received_date ?: null,
                'acquisition_source' => $row->source ?: null,
                'price' => $row->price ?: null,
                'condition' => null,
                'status' => 'available',
                'location_shelf' => $row->location_id ?: null,
                'notes' => null,
            ]);

            if (! $dryRun) {
                $copy->save();
                $this->upsertMapping('copy', $tenantId, (string) $row->item_id, (int) $copy->id, [
                    'item_code' => (string) ($row->item_code ?? ''),
                ]);
            }

            $touchedBookIds[(int) $bookMapping->fos_id] = true;
            $summary['copies']++;
        }

        if (! $dryRun) {
            foreach (array_keys($touchedBookIds) as $bookId) {
                $this->recalculateBookStock((int) $bookId);
            }
        }
    }

    protected function importMembers(
        ConnectionInterface $connection,
        int $tenantId,
        ?int $organizationId,
        int $tenantRoleId,
        int $limit,
        bool $dryRun,
        array &$summary,
    ): void {
        $query = $connection->table('member')->orderBy('member_id');
        $query = $this->slimsImportService->applySince($query, $connection, 'member', $this->since, ['last_update', 'register_date', 'member_since_date']);
        if ($limit > 0) {
            $query->limit($limit);
        }

        $rows = $query->get();
        foreach ($rows as $row) {
            $memberCode = (string) $row->member_id;
            if ($memberCode === '') {
                $summary['skipped']++;
                continue;
            }

            if ($this->shouldSkipEntity('member', $tenantId, $memberCode)) {
                $summary['skipped']++;
                continue;
            }

            [$user, $createdUser] = $this->resolveOrCreateUserForMember($tenantId, $row, $dryRun);
            if (! $user) {
                $summary['skipped']++;
                continue;
            }

            if (! $dryRun) {
                $this->ensureUserTenantRole((int) $user->id, $tenantId, $tenantRoleId);
            }

            $member = $this->findOrMakeMappedModel('member', $tenantId, $memberCode, Member::class);
            if (! $member) {
                $summary['skipped']++;
                continue;
            }

            $member->fill([
                'tenant_id' => $tenantId,
                'organization_id' => $organizationId,
                'user_id' => (int) $user->id,
                'member_number' => $memberCode,
                'member_type' => $row->member_type_id ? "slims_type_{$row->member_type_id}" : null,
                'joined_at' => $row->member_since_date ?: $row->register_date ?: null,
                'expires_at' => $row->expire_date ?: null,
                'max_books' => 3,
                'loan_period_days' => 7,
                'fine_per_day' => 0,
                'total_loans_count' => 0,
                'current_loans_count' => 0,
                'total_fines' => 0,
                'unpaid_fines' => 0,
                'status' => ((int) ($row->is_pending ?? 0) === 1)
                    ? 'inactive'
                    : $this->defaultMemberStatus,
                'suspension_reason' => null,
                'suspension_until' => null,
                'notes' => $row->member_notes ?: null,
            ]);

            if (! $dryRun) {
                $member->save();
                $this->upsertMapping('member', $tenantId, $memberCode, (int) $member->id, [
                    'email' => (string) $user->email,
                ]);
            }

            if ($createdUser) {
                $summary['users']++;
            }
            $summary['members']++;
        }
    }

    protected function importLoans(
        ConnectionInterface $connection,
        int $tenantId,
        ?int $organizationId,
        int $limit,
        bool $dryRun,
        array &$summary,
    ): void {
        $query = $connection->table('loan')
            ->leftJoin('item', 'loan.item_code', '=', 'item.item_code')
            ->select([
                'loan.loan_id',
                'loan.item_code',
                'loan.member_id',
                'loan.loan_date',
                'loan.due_date',
                'loan.renewed',
                'loan.is_lent',
                'loan.is_return',
                'loan.return_date',
                'item.item_id',
            ])
            ->orderBy('loan.loan_id');

        $query = $this->slimsImportService->applySince($query, $connection, 'loan', $this->since, ['return_date', 'due_date', 'loan_date']);

        if ($limit > 0) {
            $query->limit($limit);
        }

        $rows = $query->get();

        foreach ($rows as $row) {
            if ($this->shouldSkipEntity('loan', $tenantId, (string) $row->loan_id)) {
                $summary['skipped']++;
                continue;
            }

            $memberMapping = $this->findMapping('member', $tenantId, (string) $row->member_id);
            $copyMapping = isset($row->item_id)
                ? $this->findMapping('copy', $tenantId, (string) $row->item_id)
                : null;

            if (! $memberMapping || ! $copyMapping) {
                $summary['skipped']++;
                continue;
            }

            $loan = $this->findOrMakeMappedModel('loan', $tenantId, (string) $row->loan_id, Loan::class);
            if (! $loan) {
                $summary['skipped']++;
                continue;
            }

            $isReturned = ((int) ($row->is_return ?? 0) === 1) || ! empty($row->return_date);
            $loan->fill([
                'tenant_id' => $tenantId,
                'organization_id' => $organizationId,
                'book_copy_id' => (int) $copyMapping->fos_id,
                'member_id' => (int) $memberMapping->fos_id,
                'processed_by' => null,
                'returned_by' => null,
                'loan_date' => $row->loan_date ?: now()->toDateString(),
                'due_date' => $row->due_date ?: now()->toDateString(),
                'return_date' => $row->return_date ?: null,
                'extension_count' => (int) ($row->renewed ?? 0),
                'max_extensions' => 2,
                'status' => $isReturned ? 'returned' : 'borrowed',
                'fine_amount' => 0,
                'fine_paid' => 0,
                'fine_status' => 'none',
                'condition_on_loan' => null,
                'condition_on_return' => null,
                'notes' => null,
            ]);

            if (! $dryRun) {
                $loan->save();
                $this->upsertMapping('loan', $tenantId, (string) $row->loan_id, (int) $loan->id, [
                    'is_lent' => (int) ($row->is_lent ?? 0),
                    'is_return' => (int) ($row->is_return ?? 0),
                ]);
            }

            $summary['loans']++;
        }
    }

    protected function resolveMemberRole(int $tenantId, ?int $tenantRoleId): ?TenantRole
    {
        if ($tenantRoleId !== null && $tenantRoleId > 0) {
            return TenantRole::query()
                ->where('tenant_id', $tenantId)
                ->find($tenantRoleId);
        }

        $preferred = TenantRole::query()
            ->where('tenant_id', $tenantId)
            ->where(function ($query): void {
                $query->where('is_default', true)
                    ->orWhereRaw('LOWER(slug) like ?', ['%member%'])
                    ->orWhereRaw('LOWER(slug) like ?', ['%student%'])
                    ->orWhereRaw('LOWER(name) like ?', ['%member%'])
                    ->orWhereRaw('LOWER(name) like ?', ['%student%'])
                    ->orWhereRaw('LOWER(name) like ?', ['%siswa%']);
            })
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->first();

        if ($preferred) {
            return $preferred;
        }

        return TenantRole::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('id')
            ->first();
    }

    protected function resolveOrCreateUserForMember(int $tenantId, object $memberRow, bool $dryRun): array
    {
        $memberCode = trim((string) $memberRow->member_id);
        $name = trim((string) ($memberRow->member_name ?: "SLiMS Member {$memberCode}"));
        $email = trim((string) ($memberRow->member_email ?? ''));
        $emailDomain = $this->emailDomain;
        $username = Str::limit(Str::lower("slims_t{$tenantId}_{$memberCode}"), 120, '');

        if ($email === '' || ! str_contains($email, '@')) {
            $email = "slims_{$tenantId}_{$memberCode}@{$emailDomain}";
        }

        $user = User::withTrashed()->where('email', $email)->first();
        if (! $user) {
            $user = User::withTrashed()->where('username', $username)->first();
        }

        $created = false;
        if (! $user) {
            $created = true;
            $user = new User();
            $user->fill([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'phone' => $memberRow->member_phone ?: null,
                'password' => Hash::make(Str::random(18) . 'Aa1!'),
                'timezone' => config('app.timezone', 'UTC'),
                'locale' => config('app.locale', 'en'),
                'status' => $this->defaultMemberStatus,
                'is_super_admin' => false,
            ]);

            if (! $dryRun) {
                $user->save();
            } else {
                $user->id = -1;
            }
        } else {
            if ($user->trashed() && ! $dryRun) {
                $user->restore();
            }
        }

        return [$user, $created];
    }

    protected function ensureUserTenantRole(int $userId, int $tenantId, int $tenantRoleId): void
    {
        UserTenantRole::query()->firstOrCreate(
            [
                'user_id' => $userId,
                'tenant_id' => $tenantId,
                'organization_id' => null,
                'tenant_role_id' => $tenantRoleId,
            ],
            [
                'assigned_by' => null,
                'assigned_at' => now(),
                'is_primary' => false,
            ],
        );
    }

    protected function recalculateBookStock(int $bookId): void
    {
        $book = Book::query()->find($bookId);
        if (! $book) {
            return;
        }

        $total = BookCopy::query()->where('book_id', $bookId)->count();
        $borrowed = Loan::query()
            ->whereNull('return_date')
            ->whereIn('status', ['borrowed', 'lent', 'on_loan'])
            ->whereHas('bookCopy', fn ($query) => $query->where('book_id', $bookId))
            ->count();

        $book->forceFill([
            'total_copies' => $total,
            'available_copies' => max(0, $total - $borrowed),
        ])->save();
    }

    protected function parseAuthors(string $sor): array
    {
        $value = trim($sor);
        if ($value === '') {
            return ['Unknown'];
        }

        $parts = preg_split('/\s*[;,]\s*/', $value) ?: [];
        $parts = array_values(array_filter(array_map('trim', $parts), fn ($item) => $item !== ''));

        return $parts === [] ? [$value] : $parts;
    }

    protected function mapLanguage(string $languageId): string
    {
        return match (strtolower(trim($languageId))) {
            'id', 'ind', 'indo', 'indonesia' => 'Indonesian',
            'en', 'eng', 'english' => 'English',
            default => 'Indonesian',
        };
    }

    protected function findOrMakeMappedModel(string $entityType, int $tenantId, string $slimsId, string $modelClass): ?object
    {
        $mapping = $this->findMapping($entityType, $tenantId, $slimsId);
        if (! $mapping) {
            return new $modelClass();
        }

        $model = $modelClass::withTrashed()->find((int) $mapping->fos_id);
        if (! $model) {
            return new $modelClass();
        }

        if (method_exists($model, 'trashed') && $model->trashed()) {
            $model->restore();
        }

        return $model;
    }

    protected function findMapping(string $entityType, int $tenantId, string $slimsId): ?LibrarySlimsMapping
    {
        return LibrarySlimsMapping::query()
            ->where('tenant_id', $tenantId)
            ->where('entity_type', $entityType)
            ->where('slims_id', $slimsId)
            ->first();
    }

    protected function upsertMapping(string $entityType, int $tenantId, string $slimsId, int $fosId, array $meta = []): void
    {
        LibrarySlimsMapping::query()->updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'entity_type' => $entityType,
                'slims_id' => $slimsId,
            ],
            [
                'fos_id' => $fosId,
                'meta' => $meta,
            ],
        );
    }

    protected function slimsConnection(array $connectionConfig): ConnectionInterface
    {
        $database = (string) ($connectionConfig['database'] ?? '');
        $username = (string) ($connectionConfig['username'] ?? '');

        if ($database === '' || $username === '') {
            throw new \RuntimeException('SLiMS DB config tenant belum lengkap. Isi slims_db_database dan slims_db_username di tenant_settings.');
        }

        config(['database.connections.slims_import' => $connectionConfig]);
        DB::purge('slims_import');

        return DB::connection('slims_import');
    }

    protected function resolveTenantSlimsConfig(int $tenantId, ?int $organizationId = null): ?array
    {
        $config = $this->slimsImportService->resolveConfig($tenantId, $organizationId);

        if ($config === null) {
            return null;
        }

        $this->emailDomain = $config['email_domain'];
        $this->defaultMemberStatus = $config['default_member_status'];

        return [
            'connection' => $config['connection'],
        ];
    }

    protected function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['1', 'true', 'yes', 'on'], true);
    }

    protected function validateSlimsSchema(ConnectionInterface $connection): bool
    {
        foreach (['biblio', 'item', 'member', 'loan'] as $table) {
            try {
                $connection->table($table)->limit(1)->get();
            } catch (\Throwable) {
                return false;
            }
        }

        return true;
    }

    protected function shouldSkipEntity(string $entityType, int $tenantId, string $slimsId): bool
    {
        return $this->skipExisting && $this->slimsImportService->hasMapping($entityType, $tenantId, $slimsId);
    }
}
