<?php

namespace Modules\Cms\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Page content'))
                    ->schema([
                        TenantField::make(),
                        Select::make('site_id')
                            ->label(FilamentUi::field('site_id'))
                            ->relationship('site', 'name')
                            ->required(),
                        TextInput::make('slug')
                            ->label(FilamentUi::field('slug'))
                            ->required(),
                        TextInput::make('title_id')
                            ->label(FilamentUi::field('title_id'))
                            ->required(),
                        TextInput::make('title_en')
                            ->label(FilamentUi::field('title_en')),
                        TextInput::make('template')
                            ->label(FilamentUi::field('template'))
                            ->default('default')
                            ->required(),
                        Select::make('status')
                            ->label(FilamentUi::field('status'))
                            ->options([
                                'draft' => FilamentUi::text('Draft'),
                                'published' => FilamentUi::text('Published'),
                            ])
                            ->default('draft')
                            ->required(),
                    ])
                    ->columns(2),
                Section::make(FilamentUi::text('SEO'))
                    ->schema([
                        TextInput::make('meta_title')
                            ->label(FilamentUi::field('meta_title')),
                        Textarea::make('meta_description')
                            ->label(FilamentUi::field('meta_description'))
                            ->columnSpanFull(),
                        FileUpload::make('og_image')
                            ->label(FilamentUi::field('og_image'))
                            ->image(),
                        DateTimePicker::make('publish_at')
                            ->label(FilamentUi::field('publish_at')),
                        DateTimePicker::make('published_at')
                            ->label(FilamentUi::field('published_at')),
                    ])
                    ->columns(2),
            ]);
    }
}
