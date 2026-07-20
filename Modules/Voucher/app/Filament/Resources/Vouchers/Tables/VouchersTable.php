<?php

namespace Modules\Voucher\Filament\Resources\Vouchers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class VouchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label(FilamentUi::field('tenant.name'))->searchable(),
                TextColumn::make('title')->label(FilamentUi::field('title'))->searchable()->sortable(),
                TextColumn::make('code')->label(FilamentUi::field('code'))->searchable(),
                TextColumn::make('status')->label(FilamentUi::field('status'))->badge(),
                TextColumn::make('quota')->label(FilamentUi::field('quota'))->numeric()->sortable(),
                TextColumn::make('claimed_count')->label(FilamentUi::field('claimed_count'))->numeric()->sortable(),
                TextColumn::make('start_at')->label(FilamentUi::field('start_at'))->dateTime()->sortable(),
                TextColumn::make('end_at')->label(FilamentUi::field('end_at'))->dateTime()->sortable(),
                TextColumn::make('redemption_method')->label(FilamentUi::field('redemption_method'))->searchable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'expired' => 'Expired',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
