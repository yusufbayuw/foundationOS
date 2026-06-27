<?php

namespace Modules\Core\Filament\Support;

use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Support\FilamentUi;

class ImportTableActions
{
    /**
     * @param  class-string<Importer>  $importer
     * @return array<int, Action|ImportAction>
     */
    public static function make(string $importer): array
    {
        return [
            ImportAction::make()
                ->label(FilamentUi::text('Import Data'))
                ->importer($importer)
                ->options(function () use ($importer): array {
                    $modelClass = $importer::getModel();
                    /** @var Model $model */
                    $model = app($modelClass);

                    return [
                        'tenant_id' => current_tenant_id(),
                        'model_has_tenant_relation' => $model->isRelation('tenant'),
                    ];
                }),
            static::downloadTemplate($importer),
        ];
    }

    /**
     * @param  class-string<Importer>  $importer
     */
    public static function downloadTemplate(string $importer): Action
    {
        return Action::make('downloadImportTemplate')
            ->label(FilamentUi::text('Download Template'))
            ->icon('heroicon-o-arrow-down-tray')
            ->action(function () use ($importer) {
                $headers = array_map(
                    fn (ImportColumn $column): string => $column->getExampleHeader(),
                    $importer::getColumns(),
                );

                return response()->streamDownload(function () use ($headers): void {
                    $handle = fopen('php://output', 'w');

                    if ($handle === false) {
                        return;
                    }

                    fputcsv($handle, $headers);
                    fclose($handle);
                }, static::filename($importer), [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                ]);
            });
    }

    /**
     * @param  class-string<Importer>  $importer
     */
    protected static function filename(string $importer): string
    {
        $base = str($importer)
            ->classBasename()
            ->beforeLast('Importer')
            ->snake()
            ->append('_import_template.csv')
            ->toString();

        return $base;
    }
}
