<?php

namespace App\Console\Commands;

use App\Models\LibrarySlimsMapping;
use App\Support\TypedValue;
use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
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
            $connectionConfig = $tenantSlimsConfig['connection'];
            if (! is_array($connectionConfig)) {
                throw new \RuntimeException('SLiMS connection config is invalid.');
            }

            /** @var array<string, mixed> $connectionConfig */
            $connection = $this->slimsConnection($connectionConfig);
        } catch (\RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        if (! $this->validateSlimsSchema($connection)) {
            $this->error('Koneksi SLiMS validasi gagal. Pastikan DB SLiMS terisi dan tabel inti tersedia.');

            return self::FAILURE;
        }

        /** @var array{books: int, copies: int, members: int, users: int, loans: int, skipped: int} $summary */
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
                ['books', TypedValue::string($summary['books'])],
                ['copies', TypedValue::string($summary['copies'])],
                ['members', TypedValue::string($summary['members'])],
                ['users', TypedValue::string($summary['users'])],
                ['loans', TypedValue::string($summary['loans'])],
                ['skipped', TypedValue::string($summary['skipped'])],
            ],
        );

        $this->info($dryRun
            ? 'Dry-run import selesai. Tidak ada data FOS yang ditulis.'
            : 'Import SLiMS selesai. Jalankan ulang command ini aman (idempotent per tenant).');

        return self::SUCCESS;
    }

    /**
     * @param  array{books: int, copies: int, members: int, users: int, loans: int, skipped: int}  $summary
     */
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
            $slimsId = TypedValue::string($row->biblio_id ?? null);
            if ($this->shouldSkipEntity('book', $tenantId, $slimsId)) {
                $summary['skipped']++;

                continue;
            }
            $book = $this->findOrMakeMappedModel('book', $tenantId, $slimsId, Book::class);

            if (! $book) {
                $summary['skipped']++;

                continue;
            }

            $biblioId = TypedValue::string($row->biblio_id ?? null);
            $book->fill([
                'tenant_id' => $tenantId,
                'organization_id' => $organizationId,
                'book_category_id' => null,
                'isbn' => $row->isbn_issn ?: null,
                'isbn13' => null,
                'title' => TypedValue::string($row->title ?? null, "Untitled {$biblioId}"),
                'subtitle' => null,
                'authors' => $this->parseAuthors(TypedValue::string($row->sor ?? null)),
                'publisher' => null,
                'publication_year' => $row->publish_year ?: null,
                'publication_place' => null,
                'edition' => $row->edition ?: null,
                'volume' => null,
                'series' => $row->series_title ?: null,
                'language' => $this->mapLanguage(TypedValue::string($row->language_id ?? null, 'id')),
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
                'total_copies' => TypedValue::int($book->total_copies ?? 0),
                'available_copies' => TypedValue::int($book->available_copies ?? 0),
                'location_shelf' => null,
                'is_active' => true,
                'is_reference_only' => false,
            ]);

            if (! $dryRun) {
                $book->save();
                $this->upsertMapping('book', $tenantId, $slimsId, TypedValue::int($book->id), [
                    'title' => TypedValue::string($book->title),
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
            $itemId = TypedValue::string($row->item_id ?? null);
            if ($this->shouldSkipEntity('copy', $tenantId, $itemId)) {
                $summary['skipped']++;

                continue;
            }

            $bookMapping = $this->findMapping('book', $tenantId, TypedValue::string($row->biblio_id ?? null));
            if (! $bookMapping) {
                $summary['skipped']++;

                continue;
            }

            $copy = $this->findOrMakeMappedModel('copy', $tenantId, $itemId, BookCopy::class);
            if (! $copy) {
                $summary['skipped']++;

                continue;
            }

            $copyNumber = TypedValue::string($row->item_code ?? null, "copy-{$itemId}");
            $copy->fill([
                'tenant_id' => $tenantId,
                'organization_id' => $organizationId,
                'book_id' => TypedValue::int($bookMapping->fos_id),
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
                $this->upsertMapping('copy', $tenantId, $itemId, TypedValue::int($copy->id), [
                    'item_code' => TypedValue::string($row->item_code ?? null),
                ]);
            }

            $touchedBookIds[TypedValue::int($bookMapping->fos_id)] = true;
            $summary['copies']++;
        }

        if (! $dryRun) {
            foreach (array_keys($touchedBookIds) as $bookId) {
                $this->recalculateBookStock((int) $bookId);
            }
        }
    }

    /**
     * @param  array{books: int, copies: int, members: int, users: int, loans: int, skipped: int}  $summary
     */
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
            $memberCode = TypedValue::string($row->member_id ?? null);
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
                $this->ensureUserTenantRole(TypedValue::int($user->id), $tenantId, $tenantRoleId);
            }

            $member = $this->findOrMakeMappedModel('member', $tenantId, $memberCode, Member::class);
            if (! $member) {
                $summary['skipped']++;

                continue;
            }

            $member->fill([
                'tenant_id' => $tenantId,
                'organization_id' => $organizationId,
                'user_id' => TypedValue::int($user->id),
                'member_number' => $memberCode,
                'member_type' => $row->member_type_id ? 'slims_type_'.TypedValue::string($row->member_type_id) : null,
                'joined_at' => $row->member_since_date ?: $row->register_date ?: null,
                'expires_at' => $row->expire_date ?: null,
                'max_books' => 3,
                'loan_period_days' => 7,
                'fine_per_day' => 0,
                'total_loans_count' => 0,
                'current_loans_count' => 0,
                'total_fines' => 0,
                'unpaid_fines' => 0,
                'status' => (TypedValue::int($row->is_pending ?? 0) === 1)
                    ? 'inactive'
                    : $this->defaultMemberStatus,
                'suspension_reason' => null,
                'suspension_until' => null,
                'notes' => $row->member_notes ?: null,
            ]);

            if (! $dryRun) {
                $member->save();
                $this->upsertMapping('member', $tenantId, $memberCode, TypedValue::int($member->id), [
                    'email' => TypedValue::string($user->email),
                ]);
            }

            if ($createdUser) {
                $summary['users']++;
            }
            $summary['members']++;
        }
    }

    /**
     * @param  array{books: int, copies: int, members: int, users: int, loans: int, skipped: int}  $summary
     */
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
            $loanId = TypedValue::string($row->loan_id ?? null);
            if ($this->shouldSkipEntity('loan', $tenantId, $loanId)) {
                $summary['skipped']++;

                continue;
            }

            $memberMapping = $this->findMapping('member', $tenantId, TypedValue::string($row->member_id ?? null));
            $copyMapping = isset($row->item_id)
                ? $this->findMapping('copy', $tenantId, TypedValue::string($row->item_id))
                : null;

            if (! $memberMapping || ! $copyMapping) {
                $summary['skipped']++;

                continue;
            }

            $loan = $this->findOrMakeMappedModel('loan', $tenantId, $loanId, Loan::class);
            if (! $loan) {
                $summary['skipped']++;

                continue;
            }

            $isReturned = (TypedValue::int($row->is_return ?? 0) === 1) || ! empty($row->return_date);
            $loan->fill([
                'tenant_id' => $tenantId,
                'organization_id' => $organizationId,
                'book_copy_id' => TypedValue::int($copyMapping->fos_id),
                'member_id' => TypedValue::int($memberMapping->fos_id),
                'processed_by' => null,
                'returned_by' => null,
                'loan_date' => $row->loan_date ?: now()->toDateString(),
                'due_date' => $row->due_date ?: now()->toDateString(),
                'return_date' => $row->return_date ?: null,
                'extension_count' => TypedValue::int($row->renewed ?? 0),
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
                $this->upsertMapping('loan', $tenantId, $loanId, TypedValue::int($loan->id), [
                    'is_lent' => TypedValue::int($row->is_lent ?? 0),
                    'is_return' => TypedValue::int($row->is_return ?? 0),
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

    /**
     * @return array{0: User, 1: bool}
     */
    protected function resolveOrCreateUserForMember(int $tenantId, object $memberRow, bool $dryRun): array
    {
        $memberCode = trim(TypedValue::string($memberRow->member_id ?? null));
        $name = trim(TypedValue::string($memberRow->member_name ?? null, "SLiMS Member {$memberCode}"));
        $email = trim(TypedValue::string($memberRow->member_email ?? null));
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
            $user = new User;
            $user->fill([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'phone' => $memberRow->member_phone ?: null,
                'password' => Hash::make(Str::random(18).'Aa1!'),
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

    /**
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $modelClass
     * @return TModel
     */
    protected function findOrMakeMappedModel(string $entityType, int $tenantId, string $slimsId, string $modelClass): Model
    {
        $mapping = $this->findMapping($entityType, $tenantId, $slimsId);
        if (! $mapping) {
            return new $modelClass;
        }

        $model = $modelClass::query()->find(TypedValue::int($mapping->fos_id));
        if (! $model instanceof Model) {
            return new $modelClass;
        }

        if (method_exists($modelClass, 'withTrashed')) {
            $trashedModel = $modelClass::withTrashed()->find(TypedValue::int($mapping->fos_id));

            if ($trashedModel instanceof Model) {
                $model = $trashedModel;
            }
        }

        if (method_exists($model, 'restore') && method_exists($model, 'trashed') && $model->trashed()) {
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

    /**
     * @param  array<string, mixed>  $meta
     */
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

    /**
     * @param  array<string, mixed>  $connectionConfig
     */
    protected function slimsConnection(array $connectionConfig): ConnectionInterface
    {
        $database = TypedValue::string($connectionConfig['database'] ?? null);
        $username = TypedValue::string($connectionConfig['username'] ?? null);

        if ($database === '' || $username === '') {
            throw new \RuntimeException('SLiMS DB config tenant belum lengkap. Isi slims_db_database dan slims_db_username di tenant_settings.');
        }

        config(['database.connections.slims_import' => $connectionConfig]);
        DB::purge('slims_import');

        return DB::connection('slims_import');
    }

    /**
     * @return array{connection: array<string, mixed>}|null
     */
    protected function resolveTenantSlimsConfig(int $tenantId, ?int $organizationId = null): ?array
    {
        $config = $this->slimsImportService->resolveConfig($tenantId, $organizationId);

        if ($config === null) {
            return null;
        }

        $this->emailDomain = $config['email_domain'];
        $this->defaultMemberStatus = $config['default_member_status'];

        $connection = $config['connection'];
        if (! is_array($connection)) {
            return null;
        }

        return [
            'connection' => $connection,
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
