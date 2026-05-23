<?php

namespace Modules\Monitoring\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Models\Budget;
use Modules\Finance\Models\CustomerInvoice;
use Modules\Inventory\Models\StockAdjustment;
use Modules\Inventory\Models\StockItem;
use Modules\Inventory\Models\StockMove;
use Modules\Inventory\Models\Warehouse;
use Modules\Monitoring\Observers\AuditableObserver;
use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\VendorBill;
use Nwidart\Modules\Support\ModuleServiceProvider;

class MonitoringServiceProvider extends ModuleServiceProvider
{
    /**
     * @var array<int, class-string<Model>>
     */
    protected array $auditableModels = [
        Warehouse::class,
        StockItem::class,
        StockMove::class,
        StockAdjustment::class,
        GoodsReceipt::class,
        PurchaseOrder::class,
        VendorBill::class,
        Budget::class,
        CustomerInvoice::class,
    ];

    public function boot(): void
    {
        parent::boot();

        foreach ($this->auditableModels as $modelClass) {
            $modelClass::observe(AuditableObserver::class);
        }
    }

    /**
     * The name of the module.
     */
    protected string $name = 'Monitoring';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'monitoring';

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

    /**
     * Define module schedules.
     *
     * @param  $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }
}
