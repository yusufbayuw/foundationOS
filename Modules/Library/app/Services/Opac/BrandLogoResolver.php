<?php

namespace Modules\Library\Services\Opac;

use Illuminate\Support\Facades\Storage;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;

class BrandLogoResolver
{
    public function resolve(Tenant $tenant, ?Organization $organization = null): ?string
    {
        $candidates = array_filter([
            $organization?->logo,
            $tenant->logo,
        ]);

        foreach ($candidates as $path) {
            $url = $this->pathToUrl((string) $path);
            if ($url !== null) {
                return $url;
            }
        }

        return null;
    }

    protected function pathToUrl(string $path): ?string
    {
        $value = trim($path);

        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        if (str_starts_with($value, '/')) {
            return $value;
        }

        if (Storage::disk('public')->exists($value)) {
            return Storage::disk('public')->url($value);
        }

        return asset($value);
    }
}
