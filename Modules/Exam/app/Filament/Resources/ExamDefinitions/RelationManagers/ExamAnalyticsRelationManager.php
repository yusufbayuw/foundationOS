<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Services\ExamAnalyticsService;

class ExamAnalyticsRelationManager extends RelationManager
{
    protected static string $relationship = 'examAnalytics';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return FilamentUi::text('Analytics');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([])->paginated(false);
    }

    public function content(Schema $schema): Schema
    {
        $exam = $this->getOwnerRecord();

        if (! $exam instanceof ExamDefinition) {
            return $schema->components([]);
        }

        $analytics = app(ExamAnalyticsService::class)->build($exam);
        $components = [
            Section::make(FilamentUi::text('Analytics overview'))
                ->description(FilamentUi::text('Insights based on synced results and answers.'))
                ->schema([
                    Placeholder::make('context')
                        ->label(FilamentUi::field('exam_academic_context'))
                        ->content(FilamentUi::text(ucfirst($analytics['context']))),
                ]),
        ];

        foreach ($analytics['sections'] as $section) {
            $components[] = Section::make(FilamentUi::text($section['title']))
                ->schema([
                    Placeholder::make('section_'.$section['key'])
                        ->hiddenLabel()
                        ->content(new HtmlString($section['html'])),
                ]);
        }

        return $schema->components($components);
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
