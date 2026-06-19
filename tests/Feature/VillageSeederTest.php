<?php

namespace Ajangsupardi\PostcodeId\Tests\Feature;

use Ajangsupardi\PostcodeId\Database\Seeders\DistrictSeeder;
use Ajangsupardi\PostcodeId\Database\Seeders\ProvinceSeeder;
use Ajangsupardi\PostcodeId\Database\Seeders\RegencySeeder;
use Ajangsupardi\PostcodeId\Database\Seeders\VillageSeeder;
use Ajangsupardi\PostcodeId\Models\Village;
use Ajangsupardi\PostcodeId\Tests\TestCase;

class VillageSeederTest extends TestCase
{
    public function test_seeds_villages_from_csv(): void
    {
        $csv = <<<'CSV'
kodepos,desa,kecamatan,kab,provinsi
1234567890,Desa Test,Kecamatan Test,Kab. Test,ACEH
1234567891,Desa Test2,Kecamatan Test,Kab. Test,ACEH
1234567892,Desa Test3,Kecamatan Test,Kab. Test,ACEH
CSV;

        $this->seedSampleCsv($csv);
        $this->seed(ProvinceSeeder::class);
        $this->seed(RegencySeeder::class);
        $this->seed(DistrictSeeder::class);
        $this->seed(VillageSeeder::class);

        $this->assertDatabaseCount((new Village)->getTable(), 3);
        $this->assertDatabaseHas((new Village)->getTable(), ['name' => 'Desa Test', 'postal_code' => '1234567890']);
        $this->assertDatabaseHas((new Village)->getTable(), ['name' => 'Desa Test2', 'postal_code' => '1234567891']);
    }

    public function test_villages_belong_to_correct_district(): void
    {
        $csv = <<<'CSV'
kodepos,desa,kecamatan,kab,provinsi
1234567890,Desa A,Kec Test,Kab. Test,ACEH
1234567891,Desa B,Kec Test2,Kab. Test,ACEH
CSV;

        $this->seedSampleCsv($csv);
        $this->seed(ProvinceSeeder::class);
        $this->seed(RegencySeeder::class);
        $this->seed(DistrictSeeder::class);
        $this->seed(VillageSeeder::class);

        $this->assertDatabaseHas((new Village)->getTable(), [
            'name' => 'Desa A',
            'postal_code' => '1234567890',
        ]);
        $this->assertDatabaseHas((new Village)->getTable(), [
            'name' => 'Desa B',
            'postal_code' => '1234567891',
        ]);
    }

    public function test_seeder_is_idempotent(): void
    {
        $csv = <<<'CSV'
kodepos,desa,kecamatan,kab,provinsi
1234567890,Desa Test,Kecamatan Test,Kab. Test,ACEH
CSV;

        $this->seedSampleCsv($csv);
        $this->seed(ProvinceSeeder::class);
        $this->seed(RegencySeeder::class);
        $this->seed(DistrictSeeder::class);
        $this->seed(VillageSeeder::class);
        $count1 = Village::count();

        $this->seed(VillageSeeder::class);
        $count2 = Village::count();

        $this->assertEquals($count1, $count2);
    }
}
