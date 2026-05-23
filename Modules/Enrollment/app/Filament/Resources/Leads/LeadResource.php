<?php

namespace Modules\Enrollment\Filament\Resources\Leads;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Core\Support\FilamentUi;
use Modules\Enrollment\Filament\Resources\Leads\Pages\ListLeads;
use Modules\Enrollment\Models\Lead;

class LeadResource extends ModuleResource
{
    protected static ?string $model = Lead::class;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('full_name')->label(FilamentUi::field('full_name'))->required(),
            TextInput::make('email')->label(FilamentUi::field('email'))->email(),
            TextInput::make('phone')->label(FilamentUi::field('phone')),
            Select::make('stage')
                ->label(FilamentUi::field('stage'))
                ->options(array_combine(Lead::STAGES, Lead::STAGES))
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->label(FilamentUi::field('full_name'))->searchable(),
                TextColumn::make('email')->label(FilamentUi::field('email')),
                TextColumn::make('stage')->label(FilamentUi::field('stage'))->badge(),
                TextColumn::make('next_follow_up_at')->label(FilamentUi::field('next_follow_up_at'))->dateTime(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeads::route('/'),
        ];
    }
}
