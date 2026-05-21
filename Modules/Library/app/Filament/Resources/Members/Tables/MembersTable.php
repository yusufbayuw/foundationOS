<?php

namespace Modules\Library\Filament\Resources\Members\Tables;

use App\Filament\Imports\MemberImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(FilamentUi::field('user.name'))
                    ->searchable(),
                TextColumn::make('member_number')
                    ->label(FilamentUi::field('member_number'))
                    ->searchable(),
                TextColumn::make('member_type')
                    ->label(FilamentUi::field('member_type'))
                    ->searchable(),
                TextColumn::make('joined_at')
                    ->label(FilamentUi::field('joined_at'))
                    ->date()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label(FilamentUi::field('expires_at'))
                    ->date()
                    ->sortable(),
                TextColumn::make('max_books')
                    ->label(FilamentUi::field('max_books'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('loan_period_days')
                    ->label(FilamentUi::field('loan_period_days'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fine_per_day')
                    ->label(FilamentUi::field('fine_per_day'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_loans_count')
                    ->label(FilamentUi::field('total_loans_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('current_loans_count')
                    ->label(FilamentUi::field('current_loans_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_fines')
                    ->label(FilamentUi::field('total_fines'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unpaid_fines')
                    ->label(FilamentUi::field('unpaid_fines'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('suspension_until')
                    ->label(FilamentUi::field('suspension_until'))
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(FilamentUi::field('updated_at'))
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
                ...ImportTableActions::make(MemberImporter::class),
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
