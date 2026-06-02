<?php

namespace Modules\Cms\Services;

use Illuminate\Support\Str;
use Modules\Cms\Exceptions\DuplicatePageSlugException;
use Modules\Cms\Models\Page;

class PagePublishingService
{
    public function createDraft(
        int $tenantId,
        int $siteId,
        string $slug,
        string $titleId,
        ?string $titleEn = null,
        string $template = 'default',
    ): Page {
        $normalizedSlug = Str::slug($slug);

        if ($normalizedSlug === '') {
            throw new \InvalidArgumentException('Page slug cannot be empty.');
        }

        if (Page::withoutTenantScope()
            ->where('site_id', $siteId)
            ->where('slug', $normalizedSlug)
            ->exists()) {
            throw new DuplicatePageSlugException($normalizedSlug);
        }

        return Page::withoutTenantScope()->create([
            'tenant_id' => $tenantId,
            'site_id' => $siteId,
            'slug' => $normalizedSlug,
            'title_id' => trim($titleId),
            'title_en' => $titleEn !== null ? trim($titleEn) : null,
            'template' => $template,
            'status' => 'draft',
        ]);
    }

    public function publish(Page $page, ?\DateTimeInterface $publishedAt = null): Page
    {
        $page->forceFill([
            'status' => 'published',
            'published_at' => $publishedAt ?? now(),
        ])->save();

        return $page->fresh();
    }
}
