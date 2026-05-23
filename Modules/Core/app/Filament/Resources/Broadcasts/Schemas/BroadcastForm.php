<?php

namespace Modules\Core\Filament\Resources\Broadcasts\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class BroadcastForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('subject')
                ->label(FilamentUi::field('subject'))
                ->required(),
            Textarea::make('body')
                ->label(FilamentUi::field('body'))
                ->required()
                ->columnSpanFull(),
            Select::make('audience')
                ->label(FilamentUi::field('audience'))
                ->options([
                    'tenant' => FilamentUi::text('Tenant'),
                    'newsletter' => FilamentUi::text('Newsletter'),
                    'parent' => FilamentUi::text('Parent'),
                ])
                ->default('tenant'),
            CheckboxList::make('channels')
                ->label(FilamentUi::text('Channels'))
                ->options([
                    'database' => FilamentUi::text('Database'),
                    'mail' => FilamentUi::text('Email'),
                    'whatsapp' => FilamentUi::text('WhatsApp'),
                ])
                ->default(['database']),
            Select::make('priority')
                ->label(FilamentUi::field('priority'))
                ->options([
                    'normal' => FilamentUi::text('Normal'),
                    'emergency' => FilamentUi::text('Emergency'),
                ])
                ->default('normal'),
        ]);
    }
}
