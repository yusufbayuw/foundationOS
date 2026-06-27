<?php

namespace Modules\Library\Filament\Resources\LibraryMemberTypes;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Library\Filament\Resources\LibraryMemberTypes\Pages\CreateLibraryMemberType;
use Modules\Library\Filament\Resources\LibraryMemberTypes\Pages\EditLibraryMemberType;
use Modules\Library\Filament\Resources\LibraryMemberTypes\Pages\ListLibraryMemberTypes;
use Modules\Library\Filament\Resources\LibraryMemberTypes\Pages\ViewLibraryMemberType;
use Modules\Library\Filament\Resources\LibraryMemberTypes\RelationManagers\MembersRelationManager;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Models\LibraryMemberType;

class LibraryMemberTypeResource extends LocalizedResource
{
    protected static ?string $model = LibraryMemberType::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 41;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TenantField::make(),
            Select::make('organization_id')
                ->label(FilamentUi::field('organization_id'))
                ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                    if (current_tenant_model()) {
                        $query->where('tenant_id', current_tenant_id());
                    }
                })
                ->nullable()
                ->helperText(FilamentUi::text('Optional. Leave blank for tenant-wide data.')),
            TextInput::make('code')
                ->label(FilamentUi::field('code')),
            TextInput::make('name')
                ->label(FilamentUi::field('name'))
                ->required(),
            TextInput::make('membership_period_days')
                ->label(FilamentUi::field('membership_period_days'))
                ->numeric()
                ->default(365),
            TextInput::make('max_books')
                ->label(FilamentUi::field('max_books'))
                ->numeric()
                ->default(3),
            TextInput::make('loan_period_days')
                ->label(FilamentUi::field('loan_period_days'))
                ->numeric()
                ->default(7),
            TextInput::make('fine_per_day')
                ->label(FilamentUi::field('fine_per_day'))
                ->numeric()
                ->default(1000),
            TextInput::make('max_extensions')
                ->label(FilamentUi::field('max_extensions'))
                ->numeric()
                ->default(2),
            TextInput::make('grace_period_days')
                ->label(FilamentUi::field('grace_period_days'))
                ->numeric()
                ->default(0),
            Toggle::make('is_active')
                ->label(FilamentUi::field('is_active'))
                ->default(true),
            Textarea::make('notes')
                ->label(FilamentUi::field('notes'))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('membership_period_days')
                    ->label(FilamentUi::field('membership_period_days'))
                    ->numeric(),
                TextColumn::make('max_books')
                    ->label(FilamentUi::field('max_books'))
                    ->numeric(),
                TextColumn::make('loan_period_days')
                    ->label(FilamentUi::field('loan_period_days'))
                    ->numeric(),
                IconColumn::make('is_active')
                    ->label(FilamentUi::field('is_active'))
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label(FilamentUi::field('updated_at'))
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
