<?php

namespace Tests\Feature;

use App\Filament\Widgets\NavigationGridWidget;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Core\Models\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class NavigationGridWidgetTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_panel_uses_collapsed_navigation_groups_and_a_collapsible_sidebar(): void
    {
        $panel = Filament::getPanel('admin');
        $groups = $panel->getNavigationGroups();

        $this->assertTrue($panel->isSidebarCollapsibleOnDesktop());
        $this->assertNotEmpty($groups);

        foreach ($groups as $group) {
            $this->assertInstanceOf(NavigationGroup::class, $group);
            $this->assertTrue($group->isCollapsed());
        }
    }

    public function test_it_limits_the_initial_navigation_items_and_can_load_more(): void
    {
        $widget = $this->makeWidgetWithItems(60);

        $this->assertCount(NavigationGridWidget::ITEMS_PER_PAGE, $widget->getDisplayedItems());
        $this->assertSame(60, $widget->getVisibleItemCount());
        $this->assertTrue($widget->hasMoreVisibleItems());

        $widget->loadMore();

        $this->assertCount(NavigationGridWidget::ITEMS_PER_PAGE * 2, $widget->getDisplayedItems());
        $this->assertTrue($widget->hasMoreVisibleItems());

        $widget->loadMore();

        $this->assertCount(60, $widget->getDisplayedItems());
        $this->assertFalse($widget->hasMoreVisibleItems());
    }

    public function test_search_and_group_changes_reset_the_navigation_limit(): void
    {
        $widget = $this->makeWidgetWithItems(60);
        $widget->loadMore();

        $widget->updatedSearch();

        $this->assertSame(NavigationGridWidget::ITEMS_PER_PAGE, $widget->itemLimit);

        $widget->loadMore();
        $widget->setGroup('School');

        $this->assertSame(NavigationGridWidget::ITEMS_PER_PAGE, $widget->itemLimit);
        $this->assertSame('School', $widget->activeGroup);
        $this->assertSame('', $widget->search);

        $widget->loadMore();
        $widget->search = 'student';
        $widget->updatedActiveGroup();

        $this->assertSame(NavigationGridWidget::ITEMS_PER_PAGE, $widget->itemLimit);
        $this->assertSame('', $widget->search);
    }

    public function test_pin_action_uses_canonical_server_navigation_and_discards_tampered_entries(): void
    {
        $user = User::factory()->create([
            'pinned_menus' => [[
                'label' => 'Injected',
                'url' => 'javascript:alert(1)',
                'icon' => 'malicious-component',
            ]],
        ]);
        $this->actingAs($user);
        $widget = $this->makeWidgetWithItems(2);

        $this->assertSame([], $widget->getPinnedItems());

        $widget->togglePin('/menu/1');

        $this->assertSame([[
            'label' => 'Menu 1',
            'url' => '/menu/1',
            'icon' => 'heroicon-o-rectangle-stack',
        ]], $user->refresh()->pinned_menus);
    }

    public function test_pin_action_rejects_urls_outside_the_authorized_navigation_catalogue(): void
    {
        $this->actingAs(User::factory()->create());

        $this->expectException(NotFoundHttpException::class);

        $this->makeWidgetWithItems(2)->togglePin('https://example.test/phishing');
    }

    private function makeWidgetWithItems(int $count): NavigationGridWidget
    {
        return new class($count) extends NavigationGridWidget
        {
            public function __construct(private readonly int $count) {}

            public function getGroupedItems(): array
            {
                $items = collect(range(1, $this->count))
                    ->map(fn (int $index): array => [
                        'label' => "Menu {$index}",
                        'url' => "/menu/{$index}",
                        'icon' => 'heroicon-o-rectangle-stack',
                    ])
                    ->all();

                return ['Test' => $items];
            }
        };
    }
}
