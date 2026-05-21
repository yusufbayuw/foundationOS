<?php

namespace Modules\School\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Modules\Core\Models\AcademicPeriod;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;

class ReportCardPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Rapor Siswa';

    protected static ?string $title = 'Rapor Akademik Siswa';

    protected string $view = 'school::filament.pages.report-card';

    public ?int $academic_period_id = null;

    public ?int $class_id = null;

    public ?int $student_id = null;

    public static function getNavigationGroup(): ?string
    {
        return __('school::filament.navigation.groups.academic') ?? 'Akademik';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('academic_period_id')
                    ->label('Periode Akademik')
                    ->options(AcademicPeriod::pluck('name', 'id'))
                    ->required()
                    ->live(),
                Select::make('class_id')
                    ->label('Kelas')
                    ->options(SchoolClass::pluck('name', 'id'))
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('student_id', null)),
                Select::make('student_id')
                    ->label('Siswa')
                    ->options(function (callable $get) {
                        $classId = $get('class_id');
                        if (! $classId) {
                            return collect();
                        }

                        return Student::whereHas('classStudents', function ($q) use ($classId) {
                            $q->where('class_id', $classId);
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
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->disabled(fn () => ! $this->academic_period_id || ! $this->student_id)
                ->url(fn () => route('school.report-card.download', [
                    'student' => $this->student_id,
                    'period' => $this->academic_period_id,
                ]))
                ->openUrlInNewTab(),
        ];
    }
}
