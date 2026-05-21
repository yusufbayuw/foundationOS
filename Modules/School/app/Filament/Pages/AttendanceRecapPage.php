<?php

namespace Modules\School\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Modules\Core\Models\AcademicPeriod;
use Modules\School\Models\SchoolClass;
use Modules\School\Services\AttendanceRecapService;

class AttendanceRecapPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    // Customize navigation label based on translation or static text
    protected static ?string $navigationLabel = 'Rekapitulasi Absensi';

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

    public static function getNavigationGroup(): ?string
    {
        // Use standard group or logic here
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
                    ->required()
                    ->live(),
                Select::make('month')
                    ->label('Bulan')
                    ->options([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                    ])
                    ->required()
                    ->live(),
                Select::make('year')
                    ->label('Tahun')
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

        // Standard Filament generic tenant resolution
        $tenantId = null;
        if (filament()->hasTenancy()) {
            $tenantId = filament()->getTenant()?->id;
        }

        if (! $tenantId) {
            // Fallback for safety if somehow outside standard tenant bounds
            // Assuming tenant_id = 1 for local test if absolutely needed or user first tenant
            $tenantId = auth()->user()?->organizations()?->first()?->id ?? 1;
        }

        $service = new AttendanceRecapService;

        return $service->getStudentRecap(
            $tenantId,
            $this->academic_period_id,
            $this->class_id,
            $this->month,
            $this->year
        );
    }
}
