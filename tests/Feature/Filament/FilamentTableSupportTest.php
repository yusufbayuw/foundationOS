<?php

namespace Tests\Feature\Filament;

use Illuminate\Support\Facades\App;
use Modules\Core\Filament\Support\Tables\StatusSelectFilter;
use Modules\Core\Support\FilamentUi;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Support\PurchaseRequisitionStatusOptions;
use Tests\TestCase;

class FilamentTableSupportTest extends TestCase
{
    public function test_status_select_filter_translates_options_via_filament_ui(): void
    {
        App::setLocale('id');

        $filter = StatusSelectFilter::make(PurchaseRequisitionStatusOptions::filterLabels());
        $options = $filter->getOptions();

        $this->assertSame(FilamentUi::text('Draft'), $options['draft']);
        $this->assertSame(FilamentUi::text('In Review'), $options['in_review']);
        $this->assertSame(FilamentUi::text('Approved'), $options['approved']);

        App::setLocale(config('app.fallback_locale', 'en'));
    }
}
