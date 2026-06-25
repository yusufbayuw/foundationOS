<?php

namespace Modules\Library\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\Tenant;
use Modules\Core\Support\FilamentUi;
use Modules\Library\Enums\LoanStatus;
use Modules\Library\Enums\MemberStatus;
use Modules\Library\Models\Book;
use Modules\Library\Models\Loan;
use Modules\Library\Models\Member;

class LibraryStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 11;

    protected function getStats(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Tenant) {
            return [];
        }

        $tenantId = (int) $tenant->getKey();

        $stats = Cache::remember(
            "library.stats.{$tenantId}",
            now()->addMinutes(5),
            fn (): array => [
                'books' => Book::query()->where('tenant_id', $tenantId)->count(),
                'on_loan' => Loan::query()
                    ->where('tenant_id', $tenantId)
                    ->where('status', LoanStatus::Borrowed->value)
                    ->count(),
                'overdue' => Loan::query()
                    ->where('tenant_id', $tenantId)
                    ->where('status', LoanStatus::Borrowed->value)
                    ->whereNull('return_date')
                    ->whereDate('due_date', '<', now())
                    ->count(),
                'members' => Member::query()
                    ->where('tenant_id', $tenantId)
                    ->where('status', MemberStatus::Active->value)
                    ->count(),
            ],
        );

        return [
            Stat::make(FilamentUi::text('Total Books'), (string) $stats['books'])
                ->description(FilamentUi::text('All books in the catalog'))
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),
            Stat::make(FilamentUi::text('Books on Loan'), (string) $stats['on_loan'])
                ->description(FilamentUi::text('Currently borrowed'))
                ->descriptionIcon('heroicon-m-arrow-up-tray')
                ->color('info'),
            Stat::make(FilamentUi::text('Overdue Loans'), (string) $stats['overdue'])
                ->description(FilamentUi::text('Past due date'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            Stat::make(FilamentUi::text('Active Members'), (string) $stats['members'])
                ->description(FilamentUi::text('Members in good standing'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('Library');
    }
}
