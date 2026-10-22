<?php

namespace Modules\Helpdesk\Filament\Resources\Tickets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))->schema([
                TextInput::make('code')->label(FilamentUi::field('code')),
                TextInput::make('name')->label(FilamentUi::field('name'))->required(),
                Select::make('ticket_category_id')
                    ->label(FilamentUi::field('ticket_category_id'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('status')->label(FilamentUi::field('status'))->options([
                    'open' => FilamentUi::text('Open'),
                    'in_progress' => FilamentUi::text('In progress'),
                    'resolved' => FilamentUi::text('Resolved'),
                    'closed' => FilamentUi::text('Closed'),
                    'active' => FilamentUi::text('Active'),
                    'inactive' => FilamentUi::text('Inactive'),
                ])->default('open')->required(),
                Select::make('priority')->label(FilamentUi::field('priority'))->options([
                    'low' => FilamentUi::text('Low'),
                    'normal' => FilamentUi::text('Normal'),
                    'high' => FilamentUi::text('High'),
                    'critical' => FilamentUi::text('Critical'),
                ])->default('normal')->required(),
                Textarea::make('description')->label(FilamentUi::field('description'))->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
