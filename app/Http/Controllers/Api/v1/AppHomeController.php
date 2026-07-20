<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Support\CurrentTenant;
use Illuminate\Http\JsonResponse;
use Modules\Cms\Models\Banner;
use Modules\Cms\Models\HomepageSection;
use Modules\Donation\Models\Campaign;
use Modules\Event\Models\Event;

class AppHomeController extends Controller
{
    public function __construct(private readonly CurrentTenant $currentTenant) {}

    public function show(): JsonResponse
    {
        $tenantId = $this->currentTenant->id();

        abort_if($tenantId === null, 403, 'Tenant context is required.');

        $sections = HomepageSection::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (HomepageSection $section): array => [
                'id' => $section->id,
                'type' => $section->type,
                'title' => $section->title,
                'sort_order' => $section->sort_order,
                'items' => $this->itemsForSection($section),
            ]);

        return response()->json([
            'data' => [
                'tenant_id' => (int) $tenantId,
                'sections' => $sections,
            ],
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function itemsForSection(HomepageSection $section): array
    {
        return match ($section->type) {
            HomepageSection::TYPE_PROMO_BANNERS => $this->promoBanners($section),
            HomepageSection::TYPE_FEATURED_DONATIONS => $this->featuredDonations($section),
            HomepageSection::TYPE_FEATURED_EVENTS => $this->featuredEvents($section),
            default => [],
        };
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function promoBanners(HomepageSection $section): array
    {
        return Banner::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit($this->limit($section))
            ->get()
            ->map(fn (Banner $banner): array => [
                'id' => $banner->id,
                'title' => $banner->title_id ?? $banner->name,
                'title_en' => $banner->title_en,
                'image_path' => $banner->image_path,
                'link_url' => $banner->link_url,
                'sort_order' => $banner->sort_order,
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function featuredDonations(HomepageSection $section): array
    {
        return Campaign::query()
            ->where('status', 'active')
            ->where('is_public', true)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit($this->limit($section))
            ->get()
            ->map(fn (Campaign $campaign): array => [
                'id' => $campaign->id,
                'code' => $campaign->code,
                'name' => $campaign->name,
                'category' => $campaign->category,
                'goal_amount' => $campaign->goal_amount,
                'raised_amount' => $campaign->raised_amount,
                'description' => $campaign->description,
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function featuredEvents(HomepageSection $section): array
    {
        return Event::query()
            ->where('status', 'active')
            ->where('meta->starts_at', '>=', now()->toISOString())
            ->orderBy('meta->starts_at')
            ->orderBy('id')
            ->limit($this->limit($section))
            ->get()
            ->map(fn (Event $event): array => [
                'id' => $event->id,
                'code' => $event->code,
                'name' => $event->name,
                'description' => $event->description,
                'starts_at' => $event->meta['starts_at'] ?? null,
            ])
            ->all();
    }

    private function limit(HomepageSection $section): int
    {
        return max(1, min((int) ($section->settings['limit'] ?? 5), 20));
    }
}
