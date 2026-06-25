<?php

namespace Modules\Core\Filament\Support\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Modules\Core\Support\FilamentUi;

class CommonTableColumns
{
    public static function tenantName(bool $searchable = true): TextColumn
    {
        $column = TextColumn::make('tenant.name')
            ->label(FilamentUi::field('tenant.name'));

        if ($searchable) {
            $column->searchable();
        }

        return $column;
    }

    public static function organizationName(bool $searchable = true): TextColumn
    {
        $column = TextColumn::make('organization.name')
            ->label(FilamentUi::field('organization.name'));

        if ($searchable) {
            $column->searchable();
        }

        return $column;
    }

    public static function userName(bool $searchable = true): TextColumn
    {
        $column = TextColumn::make('user.name')
            ->label(FilamentUi::field('user.name'));

        if ($searchable) {
            $column->searchable();
        }

        return $column;
    }

    public static function statusBadge(bool $searchable = true): TextColumn
    {
        $column = TextColumn::make('status')
            ->label(FilamentUi::field('status'))
            ->badge();

        if ($searchable) {
            $column->searchable();
        }

        return $column;
    }

    public static function createdAt(bool $hiddenByDefault = true): TextColumn
    {
        return TextColumn::make('created_at')
            ->label(FilamentUi::field('created_at'))
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: $hiddenByDefault);
    }

    public static function updatedAt(bool $hiddenByDefault = true): TextColumn
    {
        return TextColumn::make('updated_at')
            ->label(FilamentUi::field('updated_at'))
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: $hiddenByDefault);
    }

    public static function booleanIcon(string $attribute, string $labelPhrase): IconColumn
    {
        return IconColumn::make($attribute)
            ->label(FilamentUi::text($labelPhrase))
            ->boolean();
    }
}
