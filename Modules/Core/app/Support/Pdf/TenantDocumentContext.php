<?php

namespace Modules\Core\Support\Pdf;

use App\Support\TypedValue;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantSetting;

class TenantDocumentContext
{
    public function __construct(
        public Tenant $tenant,
        public ?Organization $organization,
        public ?string $logoDataUri,
        public string $primaryColor,
        public ?string $stampDataUri,
        public ?string $signatureDataUri,
        public string $locale,
        public string $institutionName,
        public ?string $address,
        public ?string $phone,
        public ?string $email,
    ) {}

    public static function resolve(?Tenant $tenant = null, ?Organization $organization = null): self
    {
        if ($tenant === null) {
            $panelTenant = filament()->getTenant();
            if ($panelTenant instanceof Tenant) {
                $tenant = $panelTenant;
            } elseif ($panelTenant !== null) {
                $tenant = Tenant::query()->find(TypedValue::int($panelTenant->getKey()));
            }
        }

        if (! $tenant instanceof Tenant) {
            throw new \InvalidArgumentException('Tenant is required to build a document context.');
        }

        $organization ??= Organization::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('is_main', true)
            ->first()
            ?? Organization::query()
                ->where('tenant_id', $tenant->getKey())
                ->first();

        $branding = TenantSetting::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('group', 'branding')
            ->whereIn('key', ['brand_logo', 'primary_color', 'default_locale'])
            ->pluck('value', 'key');

        $locale = auth()->user()->preferred_locale
            ?? $branding->get('default_locale')
            ?? config('app.locale', 'id');

        $logoPath = $branding->get('brand_logo') ?: $organization?->logo;
        $primaryColor = $branding->get('primary_color') ?: '#6366f1';

        return new self(
            tenant: $tenant,
            organization: $organization,
            logoDataUri: self::fileToDataUri($logoPath),
            primaryColor: is_string($primaryColor) ? $primaryColor : '#6366f1',
            stampDataUri: self::fileToDataUri($organization?->stamp),
            signatureDataUri: self::fileToDataUri($organization?->signature),
            locale: is_string($locale) ? $locale : 'id',
            institutionName: $organization->name ?? $tenant->name,
            address: $organization?->address,
            phone: $organization?->phone,
            email: $organization?->email,
        );
    }

    public static function fileToDataUri(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            return null;
        }

        $mime = $disk->mimeType($path) ?: 'image/png';
        $contents = $disk->get($path);

        if ($contents === null) {
            return null;
        }

        return 'data:'.$mime.';base64,'.base64_encode($contents);
    }

    /**
     * @return array<string, mixed>
     */
    public function toViewData(): array
    {
        return [
            'context' => $this,
            'tenant' => $this->tenant,
            'organization' => $this->organization,
            'primaryColor' => $this->primaryColor,
            'institutionName' => $this->institutionName,
            'locale' => $this->locale,
        ];
    }
}
