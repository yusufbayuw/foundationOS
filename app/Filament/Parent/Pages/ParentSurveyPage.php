<?php

namespace App\Filament\Parent\Pages;

use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Modules\Core\Models\ParentSurvey;
use Modules\Core\Models\ParentSurveyResponse;
use Modules\Core\Support\FilamentUi;

/**
 * @property Schema $form
 */
class ParentSurveyPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentCheck;

    protected string $view = 'filament.parent.pages.parent-survey';

    /** @var array<string, mixed>|null */
    public ?array $data = null;

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Satisfaction survey');
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('feedback')
                    ->label(FilamentUi::text('Your feedback'))
                    ->required()
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $survey = ParentSurvey::query()
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('closes_at')->orWhere('closes_at', '>', now());
            })
            ->first();

        if (! $survey) {
            Notification::make()
                ->title(FilamentUi::text('No active survey'))
                ->warning()
                ->send();

            return;
        }

        ParentSurveyResponse::query()->updateOrCreate(
            [
                'parent_survey_id' => $survey->getKey(),
                'parent_user_id' => auth()->id(),
            ],
            [
                'answers' => $this->form->getState(),
            ],
        );

        Notification::make()
            ->title(FilamentUi::text('Thank you for your feedback'))
            ->success()
            ->send();

        $this->form->fill();
    }
}
