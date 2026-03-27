<?php

namespace Modules\Core\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Monitoring\Filament\Resources\FileUploads\FileUploadResource;

class UploadedFilesRelationManager extends RelationManager
{
    protected static string $relationship = 'uploadedFiles';

    public function form(Schema $schema): Schema
    {
        return FileUploadResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return FileUploadResource::table($table);
    }
}
