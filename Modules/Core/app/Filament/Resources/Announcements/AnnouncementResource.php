<?php

namespace Modules\Core\Filament\Resources\Announcements;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\Announcements\Pages\CreateAnnouncement;
use Modules\Core\Filament\Resources\Announcements\Pages\EditAnnouncement;
use Modules\Core\Filament\Resources\Announcements\Pages\ListAnnouncements;
use Modules\Core\Filament\Resources\Announcements\Schemas\AnnouncementForm;
use Modules\Core\Filament\Resources\Announcements\Tables\AnnouncementsTable;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Core\Models\Announcement;

class AnnouncementResource extends ModuleResource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return AnnouncementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnnouncementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnnouncements::route('/'),
            'create' => CreateAnnouncement::route('/create'),
            'edit' => EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
