<?php

namespace Modules\Core\Support\Filament;

use Filament\Support\Colors\Color;

readonly class TenantBranding
{
    public function __construct(
        public ?string $primaryColor = null,
        public ?string $brandLogo = null,
    ) {}

    public static function default(): self
    {
        return new self;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function filamentColors(): array
    {
        if ($this->primaryColor !== null && $this->primaryColor !== '') {
            return ['primary' => Color::hex($this->primaryColor)];
        }

        return ['primary' => Color::Indigo];
    }

    public function filamentLogoUrl(): ?string
    {
        if ($this->brandLogo === null || $this->brandLogo === '') {
            return null;
        }

        return asset('storage/'.$this->brandLogo);
    }
}
