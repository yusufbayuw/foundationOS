<?php

namespace App\Filament\Widgets;

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
     * @return array<string, list<array{label: string, url: string, icon: string}>>
     */
    public function getGroupedItems(): array
    {
        $groups = [];

        foreach (Filament::getNavigation() as $group) {
            if (! method_exists($group, 'getItems')) {
                continue;
            }

            $label = $group->getLabel() ?? FilamentUi::text('Other');
            $items = collect($group->getItems())
                ->filter(fn ($item): bool => $item->isVisible() && ! $item->isHidden())
                ->map(fn ($item): array => [
                    'label' => $item->getLabel(),
                    'url' => $item->getUrl() ?? '#',
                    'icon' => $this->resolveIcon($item->getIcon()),
                ])
                ->values()
                ->all();

            if (count($items) > 0) {
                $groups[$label] = $items;
            }
        }

        return $groups;
    }

    /**
     * @return list<array{label: string, url: string, icon: string}>
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
     * @return list<array{label: string, url: string, icon: string}>
     */
    public function getPinnedItems(): array
    {
        return auth()->user()?->pinned_menus ?? [];
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
            $value = $icon->value ?? '';

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
