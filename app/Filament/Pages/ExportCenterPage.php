<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Jobs\ExportCsv;
use Filament\Actions\Exports\Models\Export;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Models\UserTenantRole;
use Modules\Core\Support\FilamentUi;

class ExportCenterPage extends Page implements HasTable
{
    use HasPageShield;
    use InteractsWithTable;

    protected static string $routePath = '/export-center';

    protected string $view = 'filament.pages.export-center';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Export Center');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return FilamentUi::module('Monitoring');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->exportQuery())
            ->columns([
                TextColumn::make('exporter')
                    ->label(FilamentUi::field('exporter'))
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                TextColumn::make('user.name')
                    ->label(FilamentUi::text('User')),
                TextColumn::make('total_rows')
                    ->label(FilamentUi::text('Total rows'))
                    ->numeric(),
                TextColumn::make('successful_rows')
                    ->label(FilamentUi::text('Successful rows'))
                    ->numeric(),
                TextColumn::make('completed_at')
                    ->label(FilamentUi::field('completed_at'))
                    ->dateTime()
                    ->placeholder(FilamentUi::text('In progress')),
            ])
            ->recordActions([
                Action::make('download')
                    ->label(FilamentUi::text('Re-download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn (Export $record): bool => $record->completed_at !== null && $record->file_name)
                    ->url(fn (Export $record): string => Storage::disk($record->file_disk)->url($record->file_name))
                    ->openUrlInNewTab(),
                Action::make('retry')
                    ->label(FilamentUi::text('Retry failed'))
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (Export $record): bool => $record->completed_at === null)
                    ->requiresConfirmation()
                    ->action(function (Export $record): void {
                        dispatch(new ExportCsv($record, ExportFormat::Csv, []));
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function exportQuery(): Builder
    {
        $tenant = Filament::getTenant();
        $userIds = [];

        if ($tenant) {
            $userIds = UserTenantRole::query()
                ->where('tenant_id', $tenant->getKey())
                ->pluck('user_id')
                ->unique()
                ->all();
        }

        return Export::query()
            ->when($userIds !== [], fn (Builder $q) => $q->whereIn('user_id', $userIds))
            ->latest();
    }
}
