<?php

namespace App\Http\Resources\Api\v1\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Cms\Models\Page;
use Modules\Cms\Models\PageBlock;

class PublicHomepageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Page $page */
        $page = $this->resource;

        return [
            'id' => $page->id,
            'slug' => $page->slug,
            'title_id' => $page->title_id,
            'title_en' => $page->title_en,
            'template' => $page->template,
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'og_image' => $page->og_image,
            'published_at' => $page->published_at,
            'blocks' => $this->whenLoaded('blocks', fn () => $page->blocks->map(fn (PageBlock $block): array => [
                'block_type' => $block->block_type,
                'sort_order' => $block->sort_order,
                'content' => $block->content,
            ])->values()),
        ];
    }
}
