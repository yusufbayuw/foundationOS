<?php

namespace Modules\Monitoring\Filament\Resources\FileUploads\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class FileUploadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name'),
                TextInput::make('uploaded_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('uploaded_by'))
                    ->numeric(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name'),
                TextInput::make('fileable_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('fileable_type')),
                TextInput::make('fileable_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('fileable_id'))
                    ->numeric(),
                TextInput::make('collection_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('collection_name')),
                TextInput::make('disk')
                    ->label(\Modules\Core\Support\FilamentUi::field('disk'))
                    ->required()
                    ->default('public'),
                TextInput::make('directory')
                    ->label(\Modules\Core\Support\FilamentUi::field('directory')),
                TextInput::make('original_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('original_name'))
                    ->required(),
                TextInput::make('stored_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('stored_name'))
                    ->required(),
                TextInput::make('mime_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('mime_type')),
                TextInput::make('extension')
                    ->label(\Modules\Core\Support\FilamentUi::field('extension')),
                TextInput::make('file_size')
                    ->label(\Modules\Core\Support\FilamentUi::field('file_size'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('checksum')
                    ->label(\Modules\Core\Support\FilamentUi::field('checksum')),
                Toggle::make('is_public')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_public'))
                    ->required(),
                Textarea::make('metadata')
                    ->label(\Modules\Core\Support\FilamentUi::field('metadata'))
                    ->columnSpanFull(),
                DateTimePicker::make('uploaded_at'),
            ]);
    }
}
