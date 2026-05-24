<?php

namespace Modules\Cms\Filament\Resources\PageBlocks;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Cms\Filament\Resources\PageBlocks\Pages\CreatePageBlock;
use Modules\Cms\Filament\Resources\PageBlocks\Pages\EditPageBlock;
use Modules\Cms\Filament\Resources\PageBlocks\Pages\ListPageBlocks;
use Modules\Cms\Filament\Resources\PageBlocks\Pages\ViewPageBlock;
use Modules\Cms\Filament\Resources\PageBlocks\Schemas\PageBlockForm;
use Modules\Cms\Filament\Resources\PageBlocks\Schemas\PageBlockInfolist;
use Modules\Cms\Filament\Resources\PageBlocks\Tables\PageBlocksTable;
use Modules\Cms\Models\PageBlock;
use Modules\Core\Filament\Support\ModuleResource;

class PageBlockResource extends ModuleResource
{
    protected static ?string $model = PageBlock::class;

    public static function form(Schema $schema): Schema
    {
        return PageBlockForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PageBlockInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PageBlocksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPageBlocks::route('/'),
            'create' => CreatePageBlock::route('/create'),
            'view' => ViewPageBlock::route('/{record}'),
            'edit' => EditPageBlock::route('/{record}/edit'),
        ];
    }
}
