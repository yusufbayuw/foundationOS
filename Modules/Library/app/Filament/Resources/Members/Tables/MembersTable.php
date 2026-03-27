<?php

namespace Modules\Library\Filament\Resources\Members\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Core\Filament\Support\ImportTableActions;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('user.name'))
                    ->searchable(),
                TextColumn::make('member_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('member_number'))
                    ->searchable(),
                TextColumn::make('member_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('member_type'))
                    ->searchable(),
                TextColumn::make('joined_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('joined_at'))
                    ->date()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('expires_at'))
                    ->date()
                    ->sortable(),
                TextColumn::make('max_books')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_books'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('loan_period_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('loan_period_days'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fine_per_day')
                    ->label(\Modules\Core\Support\FilamentUi::field('fine_per_day'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_loans_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_loans_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('current_loans_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('current_loans_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_fines')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_fines'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unpaid_fines')
                    ->label(\Modules\Core\Support\FilamentUi::field('unpaid_fines'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('suspension_until')
                    ->label(\Modules\Core\Support\FilamentUi::field('suspension_until'))
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(\App\Filament\Imports\MemberImporter::class),
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
