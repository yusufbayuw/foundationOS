<?php

namespace Modules\PhysicalSecurity\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Modules\Core\Support\FilamentUi;
use Modules\PhysicalSecurity\Models\Visitor;
use Modules\PhysicalSecurity\Models\VisitorLog;

class VisitorKioskPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::DeviceTablet;

    protected static ?int $navigationSort = 99;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'physicalsecurity::filament.pages.visitor-kiosk';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(FilamentUi::field('name'))
                    ->required(),
                TextInput::make('id_number')
                    ->label(FilamentUi::field('id_number')),
                TextInput::make('purpose')
                    ->label(FilamentUi::field('purpose')),
            ])
            ->statePath('data');
    }

    public function checkIn(): void
    {
        $data = $this->form->getState();
        $tenantId = Filament::getTenant()?->getKey() ?? 1;

        $visitor = Visitor::query()->create([
            'tenant_id' => $tenantId,
            'name' => $data['name'],
            'code' => 'VIS-'.now()->format('YmdHis'),
            'status' => 'checked_in',
            'meta' => [
                'id_number' => $data['id_number'] ?? null,
                'purpose' => $data['purpose'] ?? null,
            ],
        ]);

        VisitorLog::query()->create([
            'tenant_id' => $tenantId,
            'visitor_id' => $visitor->getKey(),
            'name' => $visitor->name,
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);

        Notification::make()
            ->title(FilamentUi::text('Visitor checked in'))
            ->success()
            ->send();

        $this->form->fill();
    }

    public function getTitle(): string
    {
        return FilamentUi::text('Visitor kiosk');
    }
}
