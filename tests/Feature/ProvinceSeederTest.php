<?php

namespace Ajangsupardi\PostcodeId\Tests\Feature;

use Ajangsupardi\PostcodeId\Database\Seeders\ProvinceSeeder;
use Ajangsupardi\PostcodeId\Models\Province;
use Ajangsupardi\PostcodeId\Tests\TestCase;

class ProvinceSeederTest extends TestCase
{
    public function test_seeds_provinces_from_csv(): void
    {
        $this->seedSampleCsv();

        $this->seed(ProvinceSeeder::class);

        $this->assertDatabaseCount((new Province)->getTable(), 2);
        $this->assertDatabaseHas((new Province)->getTable(), ['name' => 'Aceh', 'code' => 'AC']);
        $this->assertDatabaseHas((new Province)->getTable(), ['name' => 'DKI Jakarta', 'code' => 'JK']);
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seedSampleCsv();

        $this->seed(ProvinceSeeder::class);
        $count1 = Province::count();

        $this->seed(ProvinceSeeder::class);
        $count2 = Province::count();

        $this->assertEquals($count1, $count2);
    }
}
