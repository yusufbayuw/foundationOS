<?php

namespace Modules\Procurement\Filament\Resources\RfqVendors\Tables;

use App\Filament\Imports\RfqVendorImporter;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;
use Modules\Procurement\Models\RfqVendor;
use Modules\Procurement\Services\PurchaseOrderAutoCreationService;

class RfqVendorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('requestForQuotation.id')
                    ->label(FilamentUi::field('requestForQuotation.id'))
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->label(FilamentUi::field('vendor.name'))
                    ->searchable(),
                TextColumn::make('invitation_date')
                    ->label(FilamentUi::field('invitation_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('response_deadline')
                    ->label(FilamentUi::field('response_deadline'))
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('responded_at')
                    ->label(FilamentUi::field('responded_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('quotation_amount')
                    ->label(FilamentUi::field('quotation_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quotation_document')
                    ->label(FilamentUi::field('quotation_document'))
                    ->searchable(),
                TextColumn::make('technical_score')
                    ->label(FilamentUi::field('technical_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price_score')
                    ->label(FilamentUi::field('price_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_score')
                    ->label(FilamentUi::field('total_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ranking')
                    ->label(FilamentUi::field('ranking'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_shortlisted')
                    ->label(FilamentUi::field('is_shortlisted'))
                    ->boolean(),
                IconColumn::make('is_awarded')
                    ->label(FilamentUi::field('is_awarded'))
                    ->boolean(),
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
                Action::make('awardToVendor')
                    ->label('Award to Vendor')
                    ->icon(Heroicon::Trophy)
                    ->color('success')
                    ->visible(fn (RfqVendor $record): bool => ! $record->is_awarded
                        && $record->requestForQuotation?->status !== 'awarded')
                    ->schema([
                        Textarea::make('award_reason')
                            ->label('Alasan award (opsional)')
                            ->rows(3),
                    ])
                    ->action(function (RfqVendor $record, array $data): void {
                        $po = app(PurchaseOrderAutoCreationService::class)
                            ->awardToVendor($record, auth()->id(), $data['award_reason'] ?? null);

                        Notification::make()
                            ->title('PO draft dibuat')
                            ->body("PO {$po->po_number} dibuat untuk vendor {$record->vendor?->name}.")
                            ->success()
                            ->send();
                    }),
            ])
            ->headerActions([
                ...ImportTableActions::make(RfqVendorImporter::class),
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
