<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant as BaseRegisterTenant;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Modules\Core\Models\Tenant;
use Modules\Core\Services\ProductProfileCatalog;
use Modules\Core\Services\TenantAdminProvisioner;
use Modules\Core\Services\TenantModuleProvisioner;
use Modules\Core\Support\CurrencyFormatter;
use Modules\Core\Support\FilamentUi;

class RegisterTenant extends BaseRegisterTenant
{
    public static function getLabel(): string
    {
        return FilamentUi::text('Create Organization');
    }

    public function form(Schema $schema): Schema
    {
        $productProfiles = app(ProductProfileCatalog::class);

        return $schema
            ->components([
                Wizard::make([
                    Step::make(FilamentUi::text('Organization'))
                        ->description('Identitas organisasi utama Anda')
                        ->icon(Heroicon::BuildingOffice2)
                        ->columns(2)
                        ->schema([
                            TextInput::make('name')
                                ->label(FilamentUi::field('name'))
                                ->helperText('Nama yang akan tampil pada panel dan dokumen organisasi.')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set(
                                    'code',
                                    Str::slug($state ?? '', '_'),
                                )),

                            TextInput::make('code')
                                ->label(FilamentUi::field('code'))
                                ->helperText('Kode unik permanen untuk integrasi dan referensi internal.')
                                ->required()
                                ->maxLength(50)
                                ->unique(Tenant::class, 'code')
                                ->alphaDash(),
                        ]),

                    Step::make('Profil Produk')
                        ->description('Pilih kapabilitas awal sesuai model operasional')
                        ->icon(Heroicon::RectangleStack)
                        ->schema([
                            Radio::make('product_profile')
                                ->label('Jenis organisasi')
                                ->options($productProfiles->options())
                                ->descriptions($productProfiles->descriptions())
                                ->default($productProfiles->defaultCode())
                                ->in(array_keys($productProfiles->options()))
                                ->required()
                                ->columnSpanFull(),
                        ]),

                    Step::make('Regionalisasi')
                        ->description('Atur waktu, bahasa, dan mata uang utama')
                        ->icon(Heroicon::GlobeAlt)
                        ->columns(2)
                        ->schema([
                            Select::make('timezone')
                                ->label(FilamentUi::field('timezone'))
                                ->options(
                                    collect(\DateTimeZone::listIdentifiers())
                                        ->mapWithKeys(fn (string $timezone): array => [$timezone => $timezone])
                                )
                                ->searchable()
                                ->default(config('app.timezone', 'Asia/Jakarta'))
                                ->required()
                                ->columnSpanFull(),

                            Select::make('locale')
                                ->label(FilamentUi::field('locale'))
                                ->options(['id' => 'Indonesia', 'en' => 'English'])
                                ->default('id')
                                ->required(),

                            Select::make('currency')
                                ->label(FilamentUi::field('currency'))
                                ->options(CurrencyFormatter::options())
                                ->default('IDR')
                                ->required()
                                ->searchable(),
                        ]),
                ])
                    ->submitAction(new HtmlString(Blade::render(
                        '<x-filament::button type="submit">{{ $label }}</x-filament::button>',
                        ['label' => static::getLabel()],
                    )))
                    ->columnSpanFull(),
            ]);
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function handleRegistration(array $data): Tenant
    {
        $user = auth()->user();
        Gate::authorize('create', Tenant::class);

        $productProfiles = app(ProductProfileCatalog::class);
        $profileCode = $data['product_profile'] ?? $productProfiles->defaultCode();

        return DB::transaction(function () use ($data, $productProfiles, $profileCode, $user): Tenant {
            $tenant = Tenant::create([
                'uuid' => Str::uuid(),
                'name' => $data['name'],
                'code' => $data['code'],
                'timezone' => $data['timezone'] ?? 'Asia/Jakarta',
                'locale' => $data['locale'] ?? 'id',
                'currency' => $data['currency'] ?? 'IDR',
                'status' => 'active',
                'product_profile_code' => $profileCode,
                'product_profile_version' => $productProfiles->version($profileCode),
                'created_by' => $user->getKey(),
            ]);

            $provisioner = app(TenantAdminProvisioner::class);
            $provisioner->ensureTenantOwnerRole($user, $tenant);
            $provisioner->assignShieldSuperAdmin($user, $tenant);
            app(TenantModuleProvisioner::class)->enableProfileForTenant($tenant, $profileCode);

            return $tenant;
        });
    }
}
