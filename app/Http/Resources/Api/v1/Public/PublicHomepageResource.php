<?php

namespace App\Http\Resources\Api\v1\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicHomepageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title_id' => $this->title_id,
            'title_en' => $this->title_en,
            'template' => $this->template,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'og_image' => $this->og_image,
            'published_at' => $this->published_at,
            'blocks' => $this->whenLoaded('blocks', fn () => $this->blocks->map(fn ($block): array => [
                'block_type' => $block->block_type,
                'sort_order' => $block->sort_order,
                'content' => $block->content,
            ])->values()),
        ];
    }
}
