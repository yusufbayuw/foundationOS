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
use Modules\School\Models\Student;

class ReportCardPage extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = null;

    protected static ?string $title = 'Rapor Akademik Siswa';

    protected string $view = 'school::filament.pages.report-card';

    public ?int $academic_period_id = null;

    public ?int $class_id = null;

    public ?int $student_id = null;

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Student report card');
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
                    ->live()
                    ->afterStateUpdated(function (callable $set): void {
                        $set('class_id', null);
                        $set('student_id', null);
                    }),
                Select::make('class_id')
                    ->label(FilamentUi::text('Kelas'))
                    ->options(function (callable $get) {
                        $periodId = $get('academic_period_id');

                        return $periodId
                            ? SchoolClass::query()->where('academic_period_id', $periodId)->pluck('name', 'id')
                            : collect();
                    })
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('student_id', null)),
                Select::make('student_id')
                    ->label(FilamentUi::text('Siswa'))
                    ->options(function (callable $get) {
                        $classId = $get('class_id');
                        $periodId = $get('academic_period_id');
                        if (! $classId || ! $periodId) {
                            return collect();
                        }

                        return Student::whereHas('classStudents', function ($q) use ($classId, $periodId) {
                            $q->where('class_id', $classId)
                                ->where('academic_period_id', $periodId);
                        })->with('user')->get()->pluck('user.name', 'id');
                    })
                    ->required()
                    ->live(),
            ])
            ->columns(3);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->disabled(fn () => ! $this->academic_period_id || ! $this->student_id)
                ->url(fn () => route('school.report-card.pdf', [
                    'student' => $this->student_id,
                    'period' => $this->academic_period_id,
                ]))
                ->openUrlInNewTab(),
            Action::make('downloadBulk')
                ->label(FilamentUi::text('Download bulk PDF'))
                ->icon('heroicon-o-archive-box-arrow-down')
                ->color('gray')
                ->disabled(fn () => ! $this->academic_period_id || ! $this->class_id)
                ->url(fn () => route('school.report-card.bulk.pdf', [
                    'schoolClass' => $this->class_id,
                    'period' => $this->academic_period_id,
                ]))
                ->openUrlInNewTab(),
        ];
    }
}
