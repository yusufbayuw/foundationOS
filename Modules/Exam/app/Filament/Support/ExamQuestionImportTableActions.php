<?php

namespace Modules\Exam\Filament\Support;

use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Importer;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExamQuestionImportTableActions
{
    /**
     * @param  class-string<Importer>  $importer
     * @return array<int, Action|ImportAction>
     */
    public static function make(string $importer, ?string $questionBankUuid = null): array
    {
        return [
            ...ImportTableActions::make($importer),
            static::downloadDemoCsv($questionBankUuid),
        ];
    }

    public static function downloadDemoCsv(?string $questionBankUuid = null): Action
    {
        return Action::make('downloadExamQuestionImportDemo')
            ->label(FilamentUi::text('Download demo import CSV'))
            ->icon('heroicon-o-document-arrow-down')
            ->action(function () use ($questionBankUuid): StreamedResponse {
                $path = module_path('Exam', 'resources/import/exam_question_import_demo.csv');

                return response()->streamDownload(function () use ($path, $questionBankUuid): void {
                    $contents = file_get_contents($path);

                    if ($contents === false) {
                        return;
                    }

                    if (filled($questionBankUuid)) {
                        $contents = str_replace('__BANK_UUID__', $questionBankUuid, $contents);
                    }

                    echo $contents;
                }, 'exam_question_import_demo.csv', [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                ]);
            });
    }
}
