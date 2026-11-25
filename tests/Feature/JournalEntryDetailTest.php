<?php

namespace Tests\Feature;

use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Finance\Filament\Resources\JournalEntries\Pages\ViewJournalEntry;
use Modules\Finance\Filament\Resources\JournalEntries\RelationManagers\LinesRelationManager;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
use Tests\TestCase;

class JournalEntryDetailTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_detail_page_displays_lines_and_only_posts_a_balanced_journal(): void
    {
        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'journal-detail-test',
            'name' => 'Journal Detail Test',
        ]);
        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'journal-detail-org',
            'name' => 'Journal Detail Organization',
        ]);
        $actor = User::factory()->superAdmin()->create();
        $journal = JournalEntry::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'entry_number' => 'JE-DETAIL-TEST',
            'date' => now()->toDateString(),
            'description' => 'Draft journal for detail test',
            'total_debit' => 0,
            'total_credit' => 0,
            'is_balanced' => false,
            'is_posted' => false,
        ]);

        Filament::setCurrentPanel('admin');
        $this->actingAs($actor);
        Filament::setTenant($tenant);

        Livewire::test(ViewJournalEntry::class, ['record' => $journal->getRouteKey()])
            ->assertStatus(200)
            ->assertActionVisible('postJournal')
            ->callAction('postJournal')
            ->assertNotified('Failed to post journal entry.');

        $this->assertFalse($journal->refresh()->is_posted);

        foreach ([['1000', 'Cash', 50, 0], ['2000', 'Liability', 0, 50]] as [$code, $name, $debit, $credit]) {
            $account = ChartOfAccount::query()->create([
                'tenant_id' => $tenant->id,
                'organization_id' => $organization->id,
                'code' => $code,
                'name' => $name,
            ]);
            $journal->lines()->create([
                'tenant_id' => $tenant->id,
                'chart_of_account_id' => $account->id,
                'debit' => $debit,
                'credit' => $credit,
            ]);
        }

        Livewire::withoutLazyLoading()->test(LinesRelationManager::class, ['ownerRecord' => $journal, 'pageClass' => ViewJournalEntry::class])
            ->assertCanSeeTableRecords($journal->lines()->get());

        Livewire::test(ViewJournalEntry::class, ['record' => $journal->getRouteKey()])
            ->callAction('postJournal')
            ->assertNotified('Journal entry posted.');

        $this->assertTrue($journal->refresh()->is_posted);
        $this->assertSame('50.00', $journal->total_debit);
        $this->assertSame('50.00', $journal->total_credit);
    }
}
