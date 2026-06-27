<?php

namespace Modules\Core\Filament\Pages;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Support\FilamentUi;
use Modules\Core\Support\TenantSettingsResolver;

class BrandingSettingsPage extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Swatch;

    protected string $view = 'core::filament.pages.branding-settings';

    protected static ?int $navigationSort = 6;

    public ?string $brand_logo = null;

    public ?string $primary_color = null;

    public function getTitle(): string
    {
        return FilamentUi::text('Branding Settings');
    }

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Branding Settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('Core');
    }

    public function mount(): void
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return;
        }

        $settings = app(TenantSettingsResolver::class)->group($tenant->getKey(), 'branding');

        $this->brand_logo = $settings['brand_logo'] ?? null;
        $this->primary_color = $settings['primary_color'] ?? '#6366f1';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('brand_logo')
                    ->label(FilamentUi::field('brand_logo'))
                    ->image()
                    ->visibility('public')
                    ->directory('tenant-logos')
                    ->imagePreviewHeight('80'),

                ColorPicker::make('primary_color')
                    ->label(FilamentUi::field('primary_color'))
                    ->hex()
                    ->default('#6366f1'),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(FilamentUi::text('Save'))
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return;
        }

        $data = $this->form->getState();

        foreach (['brand_logo', 'primary_color'] as $key) {
            if (isset($data[$key])) {
                TenantSetting::updateOrCreate(
                    [
                        'tenant_id' => $tenant->getKey(),
                        'group' => 'branding',
                        'key' => $key,
                    ],
                    [
                        'value' => $data[$key],
                        'type' => 'string',
                    ]
                );
            }
        }

        app(TenantSettingsResolver::class)->forgetGroup($tenant->getKey(), 'branding');

        Notification::make()
            ->title(FilamentUi::text('Branding updated'))
            ->success()
            ->send();
    }
}
