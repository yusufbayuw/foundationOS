<?php

namespace Modules\Library\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Core\Support\FilamentUi;
use Modules\Library\Models\Book;
use Modules\Library\Models\Loan;
use Modules\Library\Models\Member;

class LibraryStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 11;

    protected function getStats(): array
    {
        if (! Filament::getTenant()) {
            return [];
        }

        return [
            Stat::make('Total Books', Book::count())
                ->description('All books in the catalog')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),
            Stat::make('Books on Loan', Loan::where('status', 'borrowed')->count())
                ->description('Currently borrowed')
                ->descriptionIcon('heroicon-m-arrow-up-tray')
                ->color('info'),
            Stat::make('Overdue Loans', Loan::where('status', 'borrowed')
                ->whereNull('return_date')
                ->whereDate('due_date', '<', now())
                ->count())
                ->description('Past due date')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            Stat::make('Active Members', Member::where('status', 'active')->count())
                ->description('Members in good standing')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('Library');
    }
}
