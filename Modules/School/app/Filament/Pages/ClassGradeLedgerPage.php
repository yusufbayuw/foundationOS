<?php

namespace Modules\School\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Support\FilamentUi;
use Modules\School\Models\SchoolClass;
use Modules\School\Services\ClassGradeLedgerService;

class ClassGradeLedgerPage extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationLabel = null;

    protected static ?string $title = 'Buku Nilai Kelas';

    protected string $view = 'school::filament.pages.class-grade-ledger';

    public ?int $academic_period_id = null;

    public ?int $class_id = null;

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Class grade ledger');
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('School');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('academic_period_id')
                    ->label(FilamentUi::text('Periode Akademik'))
                    ->options(AcademicPeriod::pluck('name', 'id'))
                    ->required()
                    ->live(),
                Select::make('class_id')
                    ->label(FilamentUi::text('Kelas'))
                    ->options(SchoolClass::pluck('name', 'id'))
                    ->required()
                    ->live(),
            ])
            ->columns(2);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getLedgerPreview(): ?array
    {
        if (! $this->academic_period_id || ! $this->class_id) {
            return null;
        }

        $schoolClass = SchoolClass::query()->find($this->class_id);

        if ($schoolClass === null) {
            return null;
        }

        return app(ClassGradeLedgerService::class)->assemble($schoolClass, $this->academic_period_id);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->disabled(fn () => ! $this->academic_period_id || ! $this->class_id)
                ->url(fn () => route('school.grade-ledger.pdf', [
                    'schoolClass' => $this->class_id,
                    'period' => $this->academic_period_id,
                ]))
                ->openUrlInNewTab(),
        ];
    }
}
