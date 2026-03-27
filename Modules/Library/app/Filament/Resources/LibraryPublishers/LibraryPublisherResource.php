<?php

namespace Modules\Library\Filament\Resources\LibraryPublishers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Filament\Resources\LibraryPublishers\Pages\CreateLibraryPublisher;
use Modules\Library\Filament\Resources\LibraryPublishers\Pages\EditLibraryPublisher;
use Modules\Library\Filament\Resources\LibraryPublishers\Pages\ListLibraryPublishers;
use Modules\Library\Filament\Resources\LibraryPublishers\Pages\ViewLibraryPublisher;
use Modules\Library\Models\LibraryPublisher;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

class LibraryPublisherResource extends LocalizedResource
{
    protected static ?string $model = LibraryPublisher::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('tenant_id')
                ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                ->relationship('tenant', 'name')
                ->default(Filament::getTenant()?->getKey())
                ->disabled(Filament::getTenant() !== null)
                ->dehydrated()
                ->required(),
            Select::make('organization_id')
                ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                    if (Filament::getTenant()) {
                        $query->where('tenant_id', Filament::getTenant()->getKey());
                    }
                })
                ->nullable()
                ->helperText('Opsional. Kosongkan untuk data tenant-wide.'),
            TextInput::make('code')
                ->label(\Modules\Core\Support\FilamentUi::field('code')),
            TextInput::make('name')
                ->label(\Modules\Core\Support\FilamentUi::field('name'))
                ->required(),
            TextInput::make('email')
                ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                ->email(),
            TextInput::make('phone')
                ->label(\Modules\Core\Support\FilamentUi::field('phone')),
            TextInput::make('website')
                ->label(\Modules\Core\Support\FilamentUi::field('website'))
                ->url(),
            Toggle::make('is_active')
                ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                ->default(true),
            Textarea::make('notes')
                ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLibraryPublishers::route('/'),
            'create' => CreateLibraryPublisher::route('/create'),
            'view' => ViewLibraryPublisher::route('/{record}'),
            'edit' => EditLibraryPublisher::route('/{record}/edit'),
        ];
    }
}
