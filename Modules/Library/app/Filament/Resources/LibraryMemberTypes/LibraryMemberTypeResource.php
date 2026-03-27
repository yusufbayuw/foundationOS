<?php

namespace Modules\Library\Filament\Resources\LibraryMemberTypes;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Filament\Resources\LibraryMemberTypes\Pages\CreateLibraryMemberType;
use Modules\Library\Filament\Resources\LibraryMemberTypes\Pages\EditLibraryMemberType;
use Modules\Library\Filament\Resources\LibraryMemberTypes\Pages\ListLibraryMemberTypes;
use Modules\Library\Filament\Resources\LibraryMemberTypes\Pages\ViewLibraryMemberType;
use Modules\Library\Filament\Resources\LibraryMemberTypes\RelationManagers\MembersRelationManager;
use Modules\Library\Models\LibraryMemberType;

class LibraryMemberTypeResource extends LocalizedResource
{
    protected static ?string $model = LibraryMemberType::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 41;

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
            TextInput::make('membership_period_days')
                ->label(\Modules\Core\Support\FilamentUi::field('membership_period_days'))
                ->numeric()
                ->default(365),
            TextInput::make('max_books')
                ->label(\Modules\Core\Support\FilamentUi::field('max_books'))
                ->numeric()
                ->default(3),
            TextInput::make('loan_period_days')
                ->label(\Modules\Core\Support\FilamentUi::field('loan_period_days'))
                ->numeric()
                ->default(7),
            TextInput::make('fine_per_day')
                ->label(\Modules\Core\Support\FilamentUi::field('fine_per_day'))
                ->numeric()
                ->default(1000),
            TextInput::make('max_extensions')
                ->label(\Modules\Core\Support\FilamentUi::field('max_extensions'))
                ->numeric()
                ->default(2),
            TextInput::make('grace_period_days')
                ->label(\Modules\Core\Support\FilamentUi::field('grace_period_days'))
                ->numeric()
                ->default(0),
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
                TextColumn::make('membership_period_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('membership_period_days'))
                    ->numeric(),
                TextColumn::make('max_books')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_books'))
                    ->numeric(),
                TextColumn::make('loan_period_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('loan_period_days'))
                    ->numeric(),
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

    public static function getRelations(): array
    {
        return [
            MembersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLibraryMemberTypes::route('/'),
            'create' => CreateLibraryMemberType::route('/create'),
            'view' => ViewLibraryMemberType::route('/{record}'),
            'edit' => EditLibraryMemberType::route('/{record}/edit'),
        ];
    }
}
