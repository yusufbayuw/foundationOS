<?php

namespace Tests\Feature\Characterization;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Global\Models\City;
use Modules\Global\Models\Country;
use Modules\Global\Models\Province;
use Modules\Global\Models\Timezone;
use Tests\TestCase;

/**
 * Characterization tests for the Global reference-data module.
 * Records current behavior as-is before structural refactors.
 */
class GlobalModuleCharacterizationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_country_soft_delete_keeps_trashed_record_queryable(): void
    {
        $country = Country::create([
            'code' => 'ZZ',
            'name' => 'Zedland',
        ]);

        $country->delete();

        $this->assertSoftDeleted($country);
        $this->assertNotNull(Country::withTrashed()->find($country->id));
    }

    public function test_province_remains_linked_after_country_soft_delete(): void
    {
        $country = Country::create([
            'code' => 'AA',
            'name' => 'Alphaland',
        ]);

        $province = Province::create([
            'country_id' => $country->id,
            'code' => 'PRV',
            'name' => 'Province One',
        ]);

        $country->delete();
        $province->refresh();

        $this->assertSame($country->id, $province->country_id);
        $this->assertSame('Province One', $province->name);
    }

    public function test_timezone_persists_utc_offset_as_provided(): void
    {
        $timezone = Timezone::create([
            'code' => 'Asia/Jakarta',
            'name' => 'WIB',
            'utc_offset' => '+07:00',
        ]);

        $this->assertSame('+07:00', $timezone->fresh()->utc_offset);
    }

    public function test_city_belongs_to_province_in_current_hierarchy(): void
    {
        $country = Country::create(['code' => 'ID', 'name' => 'Indonesia']);
        $province = Province::create([
            'country_id' => $country->id,
            'code' => 'JK',
            'name' => 'Jakarta',
        ]);
        $city = City::create([
            'province_id' => $province->id,
            'code' => 'JKT',
            'name' => 'Jakarta Pusat',
        ]);

        $this->assertSame($province->id, $city->province->id);
        $this->assertSame($country->id, $city->province->country->id);
    }
}
