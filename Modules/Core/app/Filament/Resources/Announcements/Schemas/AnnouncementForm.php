<?php

namespace Modules\Core\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label(FilamentUi::field('title'))
                ->required(),
            Textarea::make('body')
                ->label(FilamentUi::field('body'))
                ->required()
                ->columnSpanFull(),
            Select::make('audience')
                ->label(FilamentUi::field('audience'))
                ->options([
                    'tenant' => FilamentUi::text('Tenant'),
                    'organization' => FilamentUi::text('Organization'),
                    'class' => FilamentUi::text('Class'),
                    'parent' => FilamentUi::text('Parent'),
                ])
                ->default('tenant'),
            DateTimePicker::make('published_at')
                ->label(FilamentUi::field('published_at')),
        ]);
    }
}
