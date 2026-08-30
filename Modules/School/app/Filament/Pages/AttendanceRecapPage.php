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
use Modules\School\Services\AttendanceRecapService;

class AttendanceRecapPage extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    // Customize navigation label based on translation or static text
    protected static ?string $navigationLabel = null;

    protected static ?string $title = 'Rekapitulasi Absensi Siswa';

    protected string $view = 'school::filament.pages.attendance-recap';

    public ?int $academic_period_id = null;

    public ?int $class_id = null;

    public ?int $month = null;

    public ?int $year = null;

    public function mount(): void
    {
        $this->month = date('n');
        $this->year = date('Y');
        $this->form->fill([
            'month' => $this->month,
            'year' => $this->year,
        ]);
    }

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Attendance recap');
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
                Select::make('month')
                    ->label(FilamentUi::text('Bulan'))
                    ->options([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                    ])
                    ->required()
                    ->live(),
                Select::make('year')
                    ->label(FilamentUi::text('Tahun'))
                    ->options(array_combine(range(date('Y') - 5, date('Y') + 1), range(date('Y') - 5, date('Y') + 1)))
                    ->required()
                    ->live(),
            ])
            ->columns(4);
    }

    public function getRecapData()
    {
        if (! $this->academic_period_id || ! $this->class_id || ! $this->month || ! $this->year) {
            return collect();
        }

        $tenantId = filament()->getTenant()?->id
            ?? SchoolClass::query()->find($this->class_id)?->tenant_id;

        if (! $tenantId) {
            return collect();
        }

        return app(AttendanceRecapService::class)->getStudentRecap(
            $tenantId,
            $this->academic_period_id,
            $this->class_id,
            $this->month,
            $this->year,
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->disabled(fn () => ! $this->academic_period_id || ! $this->class_id || ! $this->month || ! $this->year)
                ->url(fn () => route('school.attendance-recap.pdf', [
                    'schoolClass' => $this->class_id,
                    'period' => $this->academic_period_id,
                    'month' => $this->month,
                    'year' => $this->year,
                ]))
                ->openUrlInNewTab(),
        ];
    }
}
