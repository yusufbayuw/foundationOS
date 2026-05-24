<?php

namespace Modules\Cms\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('organization_id')
                    ->numeric(),
                TextInput::make('code'),
                TextInput::make('name'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('meta')
                    ->columnSpanFull(),
                Select::make('site_id')
                    ->relationship('site', 'name'),
                TextInput::make('slug'),
                TextInput::make('title_id'),
                TextInput::make('title_en'),
                TextInput::make('template')
                    ->required()
                    ->default('default'),
                TextInput::make('meta_title'),
                Textarea::make('meta_description')
                    ->columnSpanFull(),
                FileUpload::make('og_image')
                    ->image(),
                DateTimePicker::make('publish_at'),
                DateTimePicker::make('published_at'),
            ]);
    }
}
