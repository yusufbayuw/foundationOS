<?php

namespace Modules\Core\Filament\Support\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class StandardSoftDeleteTable
{
    /**
     * @return array<int, mixed>
     */
    public static function filters(): array
    {
        return [
            TrashedFilter::make(),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    public static function recordActions(bool $includeView = true, bool $includeEdit = true): array
    {
        $actions = [];

        if ($includeView) {
            $actions[] = ViewAction::make();
        }

        if ($includeEdit) {
            $actions[] = EditAction::make();
        }

        return $actions;
    }

    public static function applySoftDeleteDefaults(Table $table, bool $includeView = true, bool $includeEdit = true): Table
    {
        return $table
            ->recordActions(self::recordActions($includeView, $includeEdit))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
