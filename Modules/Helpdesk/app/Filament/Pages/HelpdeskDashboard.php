<?php

namespace Modules\Helpdesk\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Core\Support\FilamentUi;
use Modules\Helpdesk\Models\Ticket;

class HelpdeskDashboard extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::ChartBar;

    protected static ?int $navigationSort = 1;

    protected string $view = 'helpdesk::filament.pages.helpdesk-dashboard';

    public int $openTickets = 0;

    public int $escalatedTickets = 0;

    public float $avgResolutionHours = 0;

    public function mount(): void
    {
        $tenantId = Filament::getTenant()?->getKey();

        $base = Ticket::query()->when($tenantId, fn ($q) => $q->where($q->getModel()->qualifyColumn('tenant_id'), $tenantId));

        $this->openTickets = (clone $base)->whereIn('status', ['open', 'in_progress'])->count();
        $this->escalatedTickets = (clone $base)->whereNotNull('escalated_at')->count();

        $closed = (clone $base)->whereNotNull('closed_at')->get(['created_at', 'closed_at']);
        if ($closed->isNotEmpty()) {
            $this->avgResolutionHours = round(
                $closed->avg(fn (Ticket $t) => $t->created_at->diffInHours($t->closed_at)) ?? 0,
                1,
            );
        }
    }

    public function getTitle(): string
    {
        return FilamentUi::text('Helpdesk dashboard');
    }

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Helpdesk dashboard');
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('Helpdesk');
    }
}
