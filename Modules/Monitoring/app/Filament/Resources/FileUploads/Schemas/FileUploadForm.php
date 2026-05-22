<?php

namespace Modules\Monitoring\Filament\Resources\FileUploads\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class FileUploadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Context'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        TextInput::make('uploaded_by')
                            ->label(FilamentUi::field('uploaded_by'))
                            ->numeric(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        TextInput::make('fileable_type')
                            ->label(FilamentUi::field('fileable_type')),
                        TextInput::make('fileable_id')
                            ->label(FilamentUi::field('fileable_id'))
                            ->numeric(),
                        TextInput::make('collection_name')
                            ->label(FilamentUi::field('collection_name')),
                    ]),

                Section::make(FilamentUi::text('File Details'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('original_name')
                            ->label(FilamentUi::field('original_name'))
                            ->required(),
                        TextInput::make('stored_name')
                            ->label(FilamentUi::field('stored_name'))
                            ->required(),
                        TextInput::make('mime_type')
                            ->label(FilamentUi::field('mime_type')),
                        TextInput::make('extension')
                            ->label(FilamentUi::field('extension')),
                        TextInput::make('file_size')
                            ->label(FilamentUi::field('file_size'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('checksum')
                            ->label(FilamentUi::field('checksum')),
                    ]),

                Section::make(FilamentUi::text('Storage'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('disk')
                            ->label(FilamentUi::field('disk'))
                            ->required()
                            ->default('public'),
                        TextInput::make('directory')
                            ->label(FilamentUi::field('directory')),
                        Toggle::make('is_public')
                            ->label(FilamentUi::field('is_public'))
                            ->required(),
                        DateTimePicker::make('uploaded_at'),
                        Textarea::make('metadata')
                            ->label(FilamentUi::field('metadata'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
