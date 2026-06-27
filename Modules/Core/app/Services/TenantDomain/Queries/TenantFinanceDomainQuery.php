<?php

namespace Modules\Core\Services\TenantDomain\Queries;

use Modules\Core\Services\TenantDomain\Contracts\TenantDomainQueryInterface;
use Modules\Core\Services\TenantDomain\TenantDomainRelationDefinition;
use Modules\Finance\Models\Budget;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalEntryLine;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;
use Modules\Finance\Models\StudentInvoiceItem;
use Modules\Finance\Models\TuitionType;

final class TenantFinanceDomainQuery implements TenantDomainQueryInterface
{
    public function relations(): array
    {
        return [
            'chartOfAccounts' => new TenantDomainRelationDefinition('chartOfAccounts', ChartOfAccount::class),
            'tuitionTypes' => new TenantDomainRelationDefinition('tuitionTypes', TuitionType::class),
            'studentInvoices' => new TenantDomainRelationDefinition('studentInvoices', StudentInvoice::class),
            'studentInvoiceItems' => new TenantDomainRelationDefinition('studentInvoiceItems', StudentInvoiceItem::class),
            'payments' => new TenantDomainRelationDefinition('payments', Payment::class),
            'journalEntries' => new TenantDomainRelationDefinition('journalEntries', JournalEntry::class),
            'journalEntryLines' => new TenantDomainRelationDefinition('journalEntryLines', JournalEntryLine::class),
            'budgets' => new TenantDomainRelationDefinition('budgets', Budget::class),
        ];
    }
}
