<?php

namespace App\Filament\Widgets;

use App\Support\TypedValue;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Modules\Core\Models\User;
use Modules\Core\Support\FilamentUi;

class NavigationGridWidget extends Widget
{
    protected string $view = 'filament.widgets.navigation-grid-widget';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public string $search = '';

    public string $activeGroup = '';

    public function getTitle(): string
    {
        return FilamentUi::text('Navigation');
    }

    public function togglePin(string $label, string $url, string $icon): void
    {
        /** @var User $user */
        $user = auth()->user();
        $pinned = collect($user->pinned_menus ?? []);

        if ($pinned->contains('url', $url)) {
            $pinned = $pinned->reject(fn (array $item): bool => $item['url'] === $url);
        } else {
            $pinned->push(['label' => $label, 'url' => $url, 'icon' => $icon]);
        }

        $user->update(['pinned_menus' => $pinned->values()->all()]);
    }

    public function setGroup(string $group): void
    {
        $this->activeGroup = $this->activeGroup === $group ? '' : $group;
        $this->search = '';
    }

    public function updatedSearch(): void
    {
        $this->activeGroup = '';
    }

    /**
     * @return array<string, array<int, array{label: string, url: string, icon: string}>>
     */
    public function getGroupedItems(): array
    {
        $groups = [];

        foreach (Filament::getNavigation() as $group) {
            $label = TypedValue::string($group->getLabel(), FilamentUi::text('Other'));
            $items = collect($group->getItems())
                ->filter(static fn (mixed $item): bool => is_object($item) && method_exists($item, 'isVisible') && method_exists($item, 'isHidden'))
                ->filter(static fn (object $item): bool => $item->isVisible() && ! $item->isHidden())
                ->map(function (object $item): array {
                    $url = method_exists($item, 'getUrl') ? TypedValue::string($item->getUrl(), '#') : '#';
                    $icon = method_exists($item, 'getIcon') ? $item->getIcon() : null;
                    $itemLabel = method_exists($item, 'getLabel') ? TypedValue::string($item->getLabel()) : FilamentUi::text('Menu');

                    return [
                        'label' => $itemLabel,
                        'url' => $url,
                        'icon' => $this->resolveIcon($icon),
                    ];
                })
                ->values()
                ->all();

            if (count($items) > 0) {
                $groups[$label] = $items;
            }
        }

        return $groups;
    }

    /**
     * @return array<int, array{label: string, url: string, icon: string}>
     */
    public function getVisibleItems(): array
    {
        $groups = $this->getGroupedItems();

        if ($this->search !== '') {
            $term = mb_strtolower($this->search);

            return collect($groups)
                ->flatMap(fn (array $items): array => $items)
                ->filter(fn (array $item): bool => str_contains(mb_strtolower($item['label']), $term))
                ->values()
                ->all();
        }

        if ($this->activeGroup !== '') {
            return $groups[$this->activeGroup] ?? [];
        }

        return collect($groups)->flatMap(fn (array $items): array => $items)->values()->all();
    }

    /**
     * @return array<int, array{label: string, url: string, icon: string}>
     */
    public function getPinnedItems(): array
    {
        $user = TypedValue::model(auth()->user());

        /** @var array<int, array{label: string, url: string, icon: string}> $pinned */
        $pinned = $user->pinned_menus ?? [];

        return $pinned;
    }

    /**
     * @return list<string>
     */
    public function getGroupNames(): array
    {
        return array_keys($this->getGroupedItems());
    }

    public function isPinned(string $url): bool
    {
        return collect($this->getPinnedItems())->contains('url', $url);
    }

    private function resolveIcon(mixed $icon): string
    {
        if ($icon instanceof \BackedEnum) {
            $value = (string) ($icon->value ?? '');

            if ($value === '') {
                return 'heroicon-o-rectangle-stack';
            }

            if (str_starts_with($value, 'heroicon-')) {
                return $value;
            }

            // Values with a variant prefix: o-home, s-star, m-check
            if (preg_match('/^[osm]-/', $value)) {
                return 'heroicon-'.$value;
            }

            // Bare icon name: cog-6-tooth → heroicon-o-cog-6-tooth
            return 'heroicon-o-'.$value;
        }

        return is_string($icon) && $icon !== '' ? $icon : 'heroicon-o-rectangle-stack';
    }
}
