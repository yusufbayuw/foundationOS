<?php

namespace Modules\Core\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Support\FilamentUi;

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
        $tenant = current_tenant_model();

        if (! $tenant) {
            return;
        }

        $settings = TenantSetting::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('group', 'branding')
            ->whereIn('key', ['brand_logo', 'primary_color'])
            ->pluck('value', 'key');

        $this->brand_logo = $settings->get('brand_logo');
        $this->primary_color = $settings->get('primary_color') ?? '#6366f1';
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
        $tenant = current_tenant_model();

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

        Notification::make()
            ->title(FilamentUi::text('Branding updated'))
            ->success()
            ->send();
    }
}
