<?php

namespace Modules\Cms\Filament\Resources\ArticleCategories\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Cms\Filament\Resources\ArticleCategories\ArticleCategoryResource;

class CreateArticleCategory extends CreateRecord
{
    protected static string $resource = ArticleCategoryResource::class;
}
