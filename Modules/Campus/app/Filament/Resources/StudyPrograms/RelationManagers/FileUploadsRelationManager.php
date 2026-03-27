<?php

namespace Modules\Campus\Filament\Resources\StudyPrograms\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Monitoring\Filament\Resources\FileUploads\FileUploadResource;

class FileUploadsRelationManager extends RelationManager
{
    protected static string $relationship = 'fileUploads';

    public function form(Schema $schema): Schema
    {
        return FileUploadResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return FileUploadResource::table($table);
    }
}
