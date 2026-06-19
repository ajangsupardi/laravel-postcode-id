<?php

namespace Ajangsupardi\PostcodeId\Tests\Feature;

use Ajangsupardi\PostcodeId\Database\Seeders\DistrictSeeder;
use Ajangsupardi\PostcodeId\Database\Seeders\ProvinceSeeder;
use Ajangsupardi\PostcodeId\Database\Seeders\RegencySeeder;
use Ajangsupardi\PostcodeId\Models\District;
use Ajangsupardi\PostcodeId\Models\Regency;
use Ajangsupardi\PostcodeId\Tests\TestCase;

class DistrictSeederTest extends TestCase
{
    public function test_seeds_districts_from_csv(): void
    {
        $csv = <<<'CSV'
kodepos,desa,kecamatan,kab,provinsi
1234567890,Desa Test,Kecamatan Test,Kab. Test,ACEH
1234567891,Desa Test,Kecamatan Test2,Kab. Test,ACEH
1234567892,Desa Test,Kecamatan Test,Kab. Test2,ACEH
CSV;

        $this->seedSampleCsv($csv);
        $this->seed(ProvinceSeeder::class);
        $this->seed(RegencySeeder::class);
        $this->seed(DistrictSeeder::class);

        $this->assertDatabaseCount((new District)->getTable(), 3);
        $this->assertDatabaseHas((new District)->getTable(), ['name' => 'Kecamatan Test']);
        $this->assertDatabaseHas((new District)->getTable(), ['name' => 'Kecamatan Test2']);
    }

    public function test_districts_belong_to_correct_regency(): void
    {
        $csv = <<<'CSV'
kodepos,desa,kecamatan,kab,provinsi
1234567890,Desa Test,Kec A,Kab. Test,ACEH
1234567891,Desa Test,Kec B,Kab. Test2,ACEH
CSV;

        $this->seedSampleCsv($csv);
        $this->seed(ProvinceSeeder::class);
        $this->seed(RegencySeeder::class);
        $this->seed(DistrictSeeder::class);

        $regency1 = Regency::where('name', 'Kabupaten Test')->first();
        $regency2 = Regency::where('name', 'Kabupaten Test2')->first();

        $this->assertCount(1, $regency1->districts);
        $this->assertCount(1, $regency2->districts);
        $this->assertEquals('Kec A', $regency1->districts->first()->name);
        $this->assertEquals('Kec B', $regency2->districts->first()->name);
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
        $count1 = District::count();

        $this->seed(DistrictSeeder::class);
        $count2 = District::count();

        $this->assertEquals($count1, $count2);
    }
}
