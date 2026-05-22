<x-filament-panels::page>
    @php($stats = $this->getStats())

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ([
            'Draft Budgets' => $stats['budgets_draft'] ?? 0,
            'Budgets In Review' => $stats['budgets_in_review'] ?? 0,
            'Approved Budgets' => $stats['budgets_approved'] ?? 0,
            'Draft Invoices' => $stats['invoices_draft'] ?? 0,
            'Issued / Partial Invoices' => $stats['invoices_issued'] ?? 0,
            'Paid Invoices' => $stats['invoices_paid'] ?? 0,
            'Pending Payments' => $stats['payments_pending'] ?? 0,
            'Verified Payments' => $stats['payments_verified'] ?? 0,
            'Posted Journals' => $stats['journals_posted'] ?? 0,
            'Allocated Budget Amount' => number_format((float) ($stats['budget_allocated_amount'] ?? 0), 2),
            'Remaining Budget Amount' => number_format((float) ($stats['budget_remaining_amount'] ?? 0), 2),
            'Outstanding Amount' => number_format((float) ($stats['invoice_outstanding_amount'] ?? 0), 2),
            'Verified Payment Amount' => number_format((float) ($stats['payment_verified_amount'] ?? 0), 2),
        ] as $label => $value)
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $label }}</p>
                <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $value }}</p>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
