<?php

namespace Modules\Core\Services;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Str;
use InvalidArgumentException;
use LogicException;

class ProductProfileCatalog
{
    public function __construct(
        protected Repository $config,
        protected Translator $translator,
    ) {}

    /**
     * @return array<string, string>
     */
    public function options(): array
    {
        return collect($this->profiles())
            ->mapWithKeys(fn (array $profile, string $code): array => [
                $code => $this->label($code),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public function descriptions(): array
    {
        return collect($this->profiles())
            ->mapWithKeys(function (array $profile, string $code): array {
                $description = $this->description($code);
                $capabilities = $this->capabilities($code);

                if ($capabilities !== []) {
                    $description = trim($description.' '.__(
                        'core::core.product_profiles.focus',
                        ['capabilities' => implode(', ', $capabilities)],
                    ));
                }

                return [$code => $description];
            })
            ->all();
    }

    public function defaultCode(): string
    {
        $configuredDefault = (string) $this->config->get('fos.product_profiles.default', 'school');

        if ($this->exists($configuredDefault)) {
            return $configuredDefault;
        }

        $firstProfileCode = array_key_first($this->profiles());

        if ($firstProfileCode === null) {
            throw new LogicException('No FoundationOS product profiles are configured.');
        }

        return $firstProfileCode;
    }

    public function exists(string $profileCode): bool
    {
        return array_key_exists($profileCode, $this->profiles());
    }

    public function label(string $profileCode): string
    {
        $profile = $this->profile($profileCode);
        $key = "core::core.product_profiles.{$profileCode}.label";

        if ($this->translator->has($key)) {
            return (string) $this->translator->get($key);
        }

        return (string) ($profile['label'] ?? Str::headline($profileCode));
    }

    public function description(string $profileCode): string
    {
        $profile = $this->profile($profileCode);
        $key = "core::core.product_profiles.{$profileCode}.description";

        if ($this->translator->has($key)) {
            return (string) $this->translator->get($key);
        }

        return (string) ($profile['description'] ?? '');
    }

    /**
     * @return list<string>
     */
    public function capabilities(string $profileCode): array
    {
        $profile = $this->profile($profileCode);
        $key = "core::core.product_profiles.{$profileCode}.capabilities";

        if ($this->translator->has($key)) {
            $translated = $this->translator->get($key);

            if (is_array($translated)) {
                return array_values(array_filter(
                    $translated,
                    fn (mixed $capability): bool => is_string($capability),
                ));
            }
        }

        return collect($profile['capabilities'] ?? [])
            ->filter(fn (mixed $capability): bool => is_string($capability))
            ->values()
            ->all();
    }

    public function version(string $profileCode): string
    {
        $profile = $this->profile($profileCode);

        return (string) ($profile['version'] ?? '1.0');
    }

    /**
     * @return list<string>
     */
    public function setupTasks(string $profileCode): array
    {
        return collect($this->profile($profileCode)['setup_tasks'] ?? [])
            ->filter(fn (mixed $task): bool => is_string($task))
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    public function moduleCodes(string $profileCode): array
    {
        $profile = $this->profile($profileCode);

        return collect($profile['modules'] ?? [])
            ->filter(fn (mixed $moduleCode): bool => is_string($moduleCode))
            ->map(fn (string $moduleCode): string => strtolower($moduleCode))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<string, array{label: string, version: string, description: string, capabilities: list<string>, setup_tasks: list<string>, modules: list<string>}>
     */
    protected function profiles(): array
    {
        $profiles = $this->config->get('fos.product_profiles.profiles', []);

        return is_array($profiles) ? $profiles : [];
    }

    /**
     * @return array{label: string, version: string, description: string, capabilities: list<string>, setup_tasks: list<string>, modules: list<string>}
     */
    protected function profile(string $profileCode): array
    {
        $profile = $this->profiles()[$profileCode] ?? null;

        if (! is_array($profile)) {
            throw new InvalidArgumentException("Unknown FoundationOS product profile [{$profileCode}].");
        }

        return $profile;
    }
}
