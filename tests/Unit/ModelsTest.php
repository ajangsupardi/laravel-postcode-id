<?php

namespace Ajangsupardi\PostcodeId\Tests\Unit;

use Ajangsupardi\PostcodeId\Tests\TestCase;

class ModelsTest extends TestCase
{
    public function test_province_model_uses_table_prefix(): void
    {
        $model = new \Ajangsupardi\PostcodeId\Models\Province;

        $this->assertEquals('provinces', $model->getTable());

        $this->app['config']->set('postcode.table_prefix', 'kodepos_');

        $model = new \Ajangsupardi\PostcodeId\Models\Province;
        $this->assertEquals('kodepos_provinces', $model->getTable());
    }

    public function test_regency_model_uses_table_prefix(): void
    {
        $model = new \Ajangsupardi\PostcodeId\Models\Regency;
        $this->assertEquals('regencies', $model->getTable());

        $this->app['config']->set('postcode.table_prefix', 'kp_');
        $model = new \Ajangsupardi\PostcodeId\Models\Regency;
        $this->assertEquals('kp_regencies', $model->getTable());
    }

    public function test_district_model_uses_table_prefix(): void
    {
        $model = new \Ajangsupardi\PostcodeId\Models\District;
        $this->assertEquals('districts', $model->getTable());

        $this->app['config']->set('postcode.table_prefix', 'pre_');
        $model = new \Ajangsupardi\PostcodeId\Models\District;
        $this->assertEquals('pre_districts', $model->getTable());
    }

    public function test_village_model_uses_table_prefix(): void
    {
        $model = new \Ajangsupardi\PostcodeId\Models\Village;
        $this->assertEquals('villages', $model->getTable());

        $this->app['config']->set('postcode.table_prefix', 'kp_');
        $model = new \Ajangsupardi\PostcodeId\Models\Village;
        $this->assertEquals('kp_villages', $model->getTable());
    }

    public function test_province_has_regencies_relationship(): void
    {
        $province = new \Ajangsupardi\PostcodeId\Models\Province;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $province->regencies());
    }

    public function test_regency_belongs_to_province(): void
    {
        $regency = new \Ajangsupardi\PostcodeId\Models\Regency;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $regency->province());
    }

    public function test_regency_has_districts(): void
    {
        $regency = new \Ajangsupardi\PostcodeId\Models\Regency;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $regency->districts());
    }

    public function test_district_belongs_to_regency(): void
    {
        $district = new \Ajangsupardi\PostcodeId\Models\District;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $district->regency());
    }

    public function test_district_has_villages(): void
    {
        $district = new \Ajangsupardi\PostcodeId\Models\District;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $district->villages());
    }

    public function test_village_belongs_to_district(): void
    {
        $village = new \Ajangsupardi\PostcodeId\Models\Village;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $village->district());
    }

    public function test_village_postal_code_scope(): void
    {
        $village = new \Ajangsupardi\PostcodeId\Models\Village;
        $query = $village->newQuery();

        $scoped = $village->scopePostalCode($query, '12345');

        $this->assertStringContainsString('postal_code', $scoped->toRawSql());
    }
}
