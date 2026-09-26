<?php

namespace Modules\Core\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Modules\Core\Filament\Resources\AcademicPeriods\AcademicPeriodResource;
use Modules\Core\Filament\Resources\AcademicYears\AcademicYearResource;
use Modules\Core\Filament\Resources\Organizations\OrganizationResource;
use Modules\Core\Filament\Resources\UserTenantRoles\UserTenantRoleResource;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Core\Services\ProductProfileCatalog;
use Modules\Core\Services\TenantModuleProvisioner;
use Modules\Core\Services\TenantSetupProgress;
use Modules\Core\Support\FilamentUi;

class SetupCenter extends Page
{
    use HasPageShield {
        canAccess as protected canAccessViaShield;
    }

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::RocketLaunch;

    protected static ?int $navigationSort = 1;

    protected string $view = 'core::filament.pages.setup-center';

    /** @var array<string, mixed> */
    public array $setup = [];

    public function mount(TenantSetupProgress $tenantSetupProgress): void
    {
        $tenant = Filament::getTenant();

        abort_unless($tenant instanceof Tenant, 404);

        $this->setup = $tenantSetupProgress->forTenant($tenant);
        $this->setup['steps'] = collect($this->setup['steps'])
            ->map(fn (array $step): array => [
                ...$step,
                'action_url' => $this->actionUrl($step['key'], $tenant),
            ])
            ->all();
        $this->setup['next_step'] = collect($this->setup['steps'])
            ->firstWhere('is_complete', false);
    }

    public function getTitle(): string
    {
        return (string) __('core::core.setup_center.title');
    }

    public function getSubheading(): ?string
    {
        return (string) __('core::core.setup_center.subheading');
    }

    public static function getNavigationLabel(): string
    {
        return (string) __('core::core.setup_center.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('Core');
    }

    public static function canAccess(): bool
    {
        $tenant = Filament::getTenant();
        $user = Filament::auth()->user();

        if (! $tenant instanceof Tenant || ! $user instanceof User) {
            return false;
        }

        return $user->canAccessTenant($tenant)
            && $user->isTenantAdministrator($tenant)
            && static::canAccessViaShield();
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        $nextStep = $this->setup['next_step'] ?? null;

        return [
            Action::make('selectProductProfile')
                ->label((string) __(
                    $this->setup['profile']['code']
                        ? 'core::core.setup_center.actions.update_profile'
                        : 'core::core.setup_center.actions.choose_profile',
                ))
                ->icon(Heroicon::RectangleStack)
                ->authorize(fn (): bool => static::canAccess())
                ->schema([
                    Select::make('product_profile')
                        ->label((string) __('core::core.setup_center.product_profile'))
                        ->options(fn (ProductProfileCatalog $catalog): array => $catalog->options())
                        ->descriptions(fn (ProductProfileCatalog $catalog): array => $catalog->descriptions())
                        ->default($this->setup['profile']['code'])
                        ->required(),
                ])
                ->action(function (array $data, ProductProfileCatalog $catalog, TenantModuleProvisioner $provisioner): void {
                    $tenant = Filament::getTenant();

                    abort_unless($tenant instanceof Tenant && static::canAccess(), 403);

                    DB::transaction(function () use ($catalog, $data, $provisioner, $tenant): void {
                        $profileCode = (string) $data['product_profile'];

                        $provisioner->enableProfileForTenant($tenant, $profileCode);
                        $tenant->update([
                            'product_profile_code' => $profileCode,
                            'product_profile_version' => $catalog->version($profileCode),
                        ]);
                    });

                    Notification::make()
                        ->title((string) __('core::core.setup_center.actions.profile_updated'))
                        ->success()
                        ->send();

                    $this->mount(app(TenantSetupProgress::class));
                }),
            Action::make('continueSetup')
                ->label($nextStep
                    ? (string) __('core::core.setup_center.actions.continue', ['step' => $nextStep['title']])
                    : (string) __('core::core.setup_center.actions.setup_complete'))
                ->icon($nextStep['icon'] ?? Heroicon::CheckCircle)
                ->color($nextStep ? 'primary' : 'success')
                ->url($nextStep['action_url'] ?? null)
                ->disabled($nextStep === null),
            Action::make('manageModules')
                ->label((string) __('core::core.setup_center.actions.view_modules'))
                ->icon(Heroicon::PuzzlePiece)
                ->color('gray')
                ->url(ModuleMarketplace::getUrl(tenant: Filament::getTenant())),
        ];
    }

    protected function actionUrl(string $stepKey, Tenant $tenant): ?string
    {
        return match ($stepKey) {
            'profile' => null,
            'modules' => ModuleMarketplace::getUrl(tenant: $tenant),
            'branding' => BrandingSettingsPage::getUrl(tenant: $tenant),
            'organizations' => OrganizationResource::getUrl('index', tenant: $tenant),
            'team' => UserTenantRoleResource::getUrl('index', tenant: $tenant),
            'academic_year' => AcademicYearResource::getUrl('index', tenant: $tenant),
            'academic_period' => AcademicPeriodResource::getUrl('index', tenant: $tenant),
            default => null,
        };
    }
}
