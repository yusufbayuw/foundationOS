<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\Public\PublicCampaignResource;
use App\Http\Resources\Api\v1\Public\PublicHomepageResource;
use App\Http\Resources\Api\v1\Public\PublicSimpleCatalogResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Alumni\Models\JobPosting;
use Modules\Cms\Models\Page;
use Modules\Donation\Models\Campaign;
use Modules\Event\Models\Event;
use Modules\Marketplace\Models\MarketplaceProduct;

class PublicCatalogController extends Controller
{
    public function campaigns(): AnonymousResourceCollection
    {
        return PublicCampaignResource::collection(
            Campaign::query()
                ->select(['id', 'code', 'name', 'category', 'goal_amount', 'raised_amount', 'description'])
                ->where('status', 'active')
                ->where('is_public', true)
                ->latest('id')
                ->paginate(15)
        );
    }

    public function events(): AnonymousResourceCollection
    {
        return PublicSimpleCatalogResource::collection($this->activePublicCatalogQuery(Event::class)->paginate(15));
    }

    public function jobPostings(): AnonymousResourceCollection
    {
        return PublicSimpleCatalogResource::collection($this->activePublicCatalogQuery(JobPosting::class)->paginate(15));
    }

    public function products(): AnonymousResourceCollection
    {
        return PublicSimpleCatalogResource::collection($this->activePublicCatalogQuery(MarketplaceProduct::class)->paginate(15));
    }

    public function homepage(): PublicHomepageResource
    {
        $page = Page::query()
            ->select([
                'id',
                'site_id',
                'slug',
                'title_id',
                'title_en',
                'template',
                'status',
                'meta_title',
                'meta_description',
                'og_image',
                'published_at',
            ])
            ->where('slug', 'home')
            ->where('status', 'published')
            ->whereHas('site', fn (Builder $query): Builder => $query->where('is_active', true))
            ->with('blocks:id,page_id,block_type,sort_order,content')
            ->latest('published_at')
            ->firstOrFail();

        return new PublicHomepageResource($page);
    }

    private function activePublicCatalogQuery(string $modelClass): Builder
    {
        return $modelClass::query()
            ->select(['id', 'code', 'name', 'description', 'meta'])
            ->where('status', 'active')
            ->latest('id');
    }
}
