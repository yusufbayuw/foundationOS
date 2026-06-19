<?php

namespace Modules\Monitoring\Filament\Resources\FileUploads;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Monitoring\Filament\Resources\FileUploads\Pages\CreateFileUpload;
use Modules\Monitoring\Filament\Resources\FileUploads\Pages\EditFileUpload;
use Modules\Monitoring\Filament\Resources\FileUploads\Pages\ListFileUploads;
use Modules\Monitoring\Filament\Resources\FileUploads\Pages\ViewFileUpload;
use Modules\Monitoring\Filament\Resources\FileUploads\Schemas\FileUploadForm;
use Modules\Monitoring\Filament\Resources\FileUploads\Schemas\FileUploadInfolist;
use Modules\Monitoring\Filament\Resources\FileUploads\Tables\FileUploadsTable;
use Modules\Monitoring\Models\FileUpload;

class FileUploadResource extends LocalizedResource
{
    protected static ?string $model = FileUpload::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return FileUploadForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FileUploadInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FileUploadsTable::configure($table);
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
            'index' => ListFileUploads::route('/'),
            'create' => CreateFileUpload::route('/create'),
            'view' => ViewFileUpload::route('/{record}'),
            'edit' => EditFileUpload::route('/{record}/edit'),
        ];
    }
}
