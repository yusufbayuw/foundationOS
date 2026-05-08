<?php

namespace Modules\Employee\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Modules\Employee\Models\AttendanceLog;
use Modules\Employee\Observers\AttendanceLogObserver;
use Modules\Employee\Console\Commands\GeneratePayrollCommand;

class EmployeeServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Employee';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'employee';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    protected array $commands = [
        GeneratePayrollCommand::class,
    ];

    public function boot(): void
    {
        parent::boot();

        AttendanceLog::observe(AttendanceLogObserver::class);
    }
}
