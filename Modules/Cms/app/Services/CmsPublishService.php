<?php

namespace Modules\Cms\Services;

use Modules\Cms\Models\Article;
use Modules\Cms\Models\Page;
use RuntimeException;

class CmsPublishService
{
    public function publishPage(Page $page): Page
    {
        if ($page->status !== 'approved' && $page->status !== 'draft') {
            throw new RuntimeException('Page must be approved before publishing.');
        }

        if ($page->publish_at && $page->publish_at->isFuture()) {
            throw new RuntimeException('Scheduled publish date has not been reached.');
        }

        $page->forceFill([
            'status' => 'published',
            'published_at' => now(),
        ])->save();

        return $page->fresh();
    }

    public function publishArticle(Article $article): Article
    {
        if (! in_array($article->status, ['approved', 'draft'], true)) {
            throw new RuntimeException('Article must be approved before publishing.');
        }

        if ($article->publish_at && $article->publish_at->isFuture()) {
            throw new RuntimeException('Scheduled publish date has not been reached.');
        }

        $article->forceFill([
            'status' => 'published',
            'published_at' => now(),
        ])->save();

        return $article->fresh();
    }
}
