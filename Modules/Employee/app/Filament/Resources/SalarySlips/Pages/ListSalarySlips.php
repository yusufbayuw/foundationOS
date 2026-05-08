<?php

namespace Modules\Employee\Filament\Resources\SalarySlips\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Modules\Employee\Filament\Resources\SalarySlips\SalarySlipResource;
use Modules\Employee\Models\Employee;
use Modules\Employee\Services\PayrollCalculationService;
use Throwable;

class ListSalarySlips extends ListRecords
{
    protected static string $resource = SalarySlipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generatePayroll')
                ->label('Generate Payroll')
                ->icon('heroicon-o-calculator')
                ->color('warning')
                ->form([
                    Select::make('employee_id')
                        ->label('Karyawan')
                        ->options(fn () => Employee::query()
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id'))
                        ->searchable()
                        ->required(),

                    Select::make('period_month')
                        ->label('Bulan')
                        ->options([
                            '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
                            '4' => 'April', '5' => 'Mei', '6' => 'Juni',
                            '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                        ])
                        ->default(now()->month)
                        ->required(),

                    TextInput::make('period_year')
                        ->label('Tahun')
                        ->numeric()
                        ->default(now()->year)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        $employee = Employee::findOrFail($data['employee_id']);
                        $slip = app(PayrollCalculationService::class)->generate(
                            $employee,
                            (int) $data['period_month'],
                            (int) $data['period_year'],
                        );
                        Notification::make()
                            ->title("Slip gaji {$employee->full_name} berhasil di-generate.")
                            ->body("Net salary: Rp " . number_format($slip->net_salary, 0, ',', '.'))
                            ->success()
                            ->send();
                    } catch (Throwable $e) {
                        Notification::make()->title('Generate payroll gagal.')->body($e->getMessage())->danger()->send();
                    }
                }),

            CreateAction::make(),
        ];
    }
}
