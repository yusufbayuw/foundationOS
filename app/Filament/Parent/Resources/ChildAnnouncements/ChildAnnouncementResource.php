<?php

namespace App\Filament\Parent\Resources\ChildAnnouncements;

use App\Filament\Parent\Resources\ChildAnnouncements\Pages\ListChildAnnouncements;
use App\Filament\Parent\Resources\ChildAnnouncements\Tables\ChildAnnouncementsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\Announcement;
use Modules\Core\Models\ParentStudent;
use Modules\Core\Support\FilamentUi;

class ChildAnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Megaphone;

    protected static ?string $slug = 'announcements';

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Announcements');
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantIds = ParentStudent::query()
            ->where('parent_user_id', auth()->id())
            ->pluck('tenant_id')
            ->unique();

        return parent::getEloquentQuery()
            ->whereIn('tenant_id', $tenantIds)
            ->whereNotNull('published_at');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return ChildAnnouncementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChildAnnouncements::route('/'),
        ];
    }
}
