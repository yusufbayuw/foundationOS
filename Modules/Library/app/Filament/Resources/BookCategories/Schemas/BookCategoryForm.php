<?php

namespace Modules\Library\Filament\Resources\BookCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class BookCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Scope'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                                if (current_tenant_model()) {
                                    $query->where('tenant_id', current_tenant_id());
                                }
                            })
                            ->nullable()
                            ->helperText(FilamentUi::text('Leave blank for tenant-wide categories.')),
                    ]),

                Section::make(FilamentUi::text('Category Details'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->default(true)
                            ->required(),
                    ]),
            ]);
    }
}
