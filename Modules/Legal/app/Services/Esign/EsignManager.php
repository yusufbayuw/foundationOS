<?php

namespace Modules\Legal\Services\Esign;

use InvalidArgumentException;
use Modules\Legal\Contracts\EsignProvider;

class EsignManager
{
    /** @var array<string, EsignProvider> */
    protected array $providers = [];

    public function register(EsignProvider $provider): void
    {
        $this->providers[$provider->providerKey()] = $provider;
    }

    public function driver(?string $name = null): EsignProvider
    {
        $name ??= config('legal.esign.default', 'manual');

        if (! isset($this->providers[$name])) {
            throw new InvalidArgumentException("E-sign provider [{$name}] is not registered.");
        }

        return $this->providers[$name];
    }
}
