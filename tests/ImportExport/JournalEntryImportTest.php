<?php

namespace Tests\ImportExport;

use App\Filament\Imports\JournalEntryImporter;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Finance\Models\JournalEntry;
use Tests\TestCase;

class JournalEntryImportTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_journal_csv_import_cannot_set_posted_or_reversed_state(): void
    {
        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'journal-import-test',
            'name' => 'Journal Import Test',
        ]);
        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'journal-import-org',
            'name' => 'Journal Import Organization',
        ]);
        $row = [
            'organization_id' => (string) $organization->id,
            'entry_number' => 'CSV-DRAFT',
            'date' => now()->toDateString(),
            'description' => 'Imported draft',
            'total_debit' => '150.00',
            'total_credit' => '150.00',
            'is_balanced' => 'true',
            'is_posted' => 'true',
            'is_reversed' => 'true',
        ];

        $importer = new JournalEntryImporter(
            new Import,
            array_combine(array_keys($row), array_keys($row)),
            ['tenant_id' => $tenant->id],
        );
        $importer($row);

        $journal = JournalEntry::query()->where('entry_number', 'CSV-DRAFT')->firstOrFail();
        $this->assertSame($tenant->id, (int) $journal->tenant_id);
        $this->assertFalse($journal->is_balanced);
        $this->assertFalse($journal->is_posted);
        $this->assertFalse($journal->is_reversed);
        $this->assertNull($journal->posted_by);
        $this->assertNull($journal->posted_at);
    }
}
