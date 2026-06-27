<?php

namespace Modules\School\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Core\Support\FilamentUi;
use Modules\School\Models\Attendance;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Modules\School\Models\Teacher;

class SchoolStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 10;

    protected function getStats(): array
    {
        $tenantId = current_tenant_id();

        if (! $tenantId) {
            return [];
        }

        $totalStudents = Student::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->count();

        $totalTeachers = Teacher::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->count();

        $activeClasses = SchoolClass::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->count();

        $todayAttendances = Attendance::query()
            ->where('tenant_id', $tenantId)
            ->whereDate('attendance_date', now()->toDateString())
            ->count();

        $todayPresent = Attendance::query()
            ->where('tenant_id', $tenantId)
            ->whereDate('attendance_date', now()->toDateString())
            ->where('status', 'present')
            ->count();

        $attendanceRate = $todayAttendances > 0
            ? round(($todayPresent / $todayAttendances) * 100, 1).'%'
            : '-';

        return [
            Stat::make('Siswa Aktif', number_format($totalStudents))
                ->description('Total siswa berstatus aktif')
                ->icon('heroicon-o-users')
                ->color('success'),
            Stat::make('Guru Aktif', number_format($totalTeachers))
                ->description('Total guru berstatus aktif')
                ->icon('heroicon-o-academic-cap')
                ->color('info'),
            Stat::make('Kelas Aktif', number_format($activeClasses))
                ->description('Kelas yang sedang berjalan')
                ->icon('heroicon-o-building-office')
                ->color('warning'),
            Stat::make('Kehadiran Hari Ini', $attendanceRate)
                ->description("{$todayPresent} dari {$todayAttendances} tercatat")
                ->icon('heroicon-o-clipboard-document-check')
                ->color($todayAttendances > 0 ? 'success' : 'gray'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('School');
    }
}
