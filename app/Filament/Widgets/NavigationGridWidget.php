<?php

namespace App\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Modules\Core\Models\User;
use Modules\Core\Support\FilamentUi;

class NavigationGridWidget extends Widget
{
    public const ITEMS_PER_PAGE = 24;

    protected string $view = 'filament.widgets.navigation-grid-widget';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public string $search = '';

    public string $activeGroup = '';

    public int $itemLimit = self::ITEMS_PER_PAGE;

    public function getTitle(): string
    {
        return FilamentUi::text('Navigation');
    }

    public function togglePin(string $url): void
    {
        $item = $this->findNavigationItem($url);

        abort_unless($item !== null, 404);

        /** @var User $user */
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $pinned = collect($this->getPinnedItems());

        if ($pinned->contains('url', $url)) {
            $pinned = $pinned->reject(fn (array $item): bool => $item['url'] === $url);
        } else {
            $pinned->push($item);
        }

        $user->update(['pinned_menus' => $pinned->values()->all()]);
    }

    public function setGroup(string $group): void
    {
        $this->activeGroup = $this->activeGroup === $group ? '' : $group;
        $this->search = '';
        $this->resetItemLimit();
    }

    public function updatedSearch(): void
    {
        $this->activeGroup = '';
        $this->resetItemLimit();
    }

    public function updatedActiveGroup(): void
    {
        $this->search = '';
        $this->resetItemLimit();
    }

    public function loadMore(): void
    {
        $this->itemLimit += self::ITEMS_PER_PAGE;
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
    public function getDisplayedItems(): array
    {
        return array_slice($this->getVisibleItems(), 0, $this->itemLimit);
    }

    public function getVisibleItemCount(): int
    {
        return count($this->getVisibleItems());
    }

    public function hasMoreVisibleItems(): bool
    {
        return $this->getVisibleItemCount() > $this->itemLimit;
    }

    /**
     * @return list<array{label: string, url: string, icon: string}>
     */
    public function getPinnedItems(): array
    {
        $itemsByUrl = collect($this->getGroupedItems())
            ->flatten(1)
            ->keyBy('url');

        return collect(auth()->user()?->pinned_menus ?? [])
            ->map(function (mixed $item) use ($itemsByUrl): ?array {
                if (! is_array($item) || ! is_string($item['url'] ?? null)) {
                    return null;
                }

                $navigationItem = $itemsByUrl->get($item['url']);

                return is_array($navigationItem) ? $navigationItem : null;
            })
            ->filter()
            ->values()
            ->all();
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

    /**
     * @return array{label: string, url: string, icon: string}|null
     */
    private function findNavigationItem(string $url): ?array
    {
        $item = collect($this->getGroupedItems())
            ->flatten(1)
            ->first(fn (mixed $item): bool => is_array($item) && ($item['url'] ?? null) === $url);

        return is_array($item) ? $item : null;
    }

    private function resetItemLimit(): void
    {
        $this->itemLimit = self::ITEMS_PER_PAGE;
    }
}
