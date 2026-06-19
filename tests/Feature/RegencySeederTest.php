<?php

namespace Ajangsupardi\PostcodeId\Tests\Feature;

use Ajangsupardi\PostcodeId\Database\Seeders\ProvinceSeeder;
use Ajangsupardi\PostcodeId\Database\Seeders\RegencySeeder;
use Ajangsupardi\PostcodeId\Models\Province;
use Ajangsupardi\PostcodeId\Models\Regency;
use Ajangsupardi\PostcodeId\Tests\TestCase;

class RegencySeederTest extends TestCase
{
    public function test_seeds_regencies_from_csv(): void
    {
        $csv = <<<'CSV'
kodepos,desa,kecamatan,kab,provinsi
1234567890,Desa Test,Kec Test,Kab. Test,ACEH
1234567891,Desa Test,Kec Test,Kab. Test2,ACEH
1234567892,Desa Test,Kec Test,KOTA BANDUNG,JAWA BARAT
CSV;

        $this->seedSampleCsv($csv);
        $this->seed(ProvinceSeeder::class);
        $this->seed(RegencySeeder::class);

        $this->assertDatabaseCount((new Regency)->getTable(), 3);
        $this->assertDatabaseHas((new Regency)->getTable(), ['name' => 'Kabupaten Test']);
        $this->assertDatabaseHas((new Regency)->getTable(), ['name' => 'Kabupaten Test2']);
        $this->assertDatabaseHas((new Regency)->getTable(), ['name' => 'Kota Bandung']);
    }

    public function test_regencies_belong_to_correct_province(): void
    {
        $csv = <<<'CSV'
kodepos,desa,kecamatan,kab,provinsi
1234567890,Desa Test,Kec Test,Kab. Test,ACEH
1234567891,Desa Test,Kec Test,KOTA JAKARTA,DKI JAKARTA
CSV;

        $this->seedSampleCsv($csv);
        $this->seed(ProvinceSeeder::class);
        $this->seed(RegencySeeder::class);

        $aceh = Province::where('name', 'Aceh')->first();
        $jakarta = Province::where('name', 'DKI Jakarta')->first();

        $this->assertCount(1, $aceh->regencies);
        $this->assertCount(1, $jakarta->regencies);
        $this->assertEquals('Kabupaten Test', $aceh->regencies->first()->name);
        $this->assertEquals('Kota Jakarta', $jakarta->regencies->first()->name);
    }

    public function test_seeder_is_idempotent(): void
    {
        $csv = <<<'CSV'
kodepos,desa,kecamatan,kab,provinsi
1234567890,Desa Test,Kec Test,Kab. Test,ACEH
CSV;

        $this->seedSampleCsv($csv);
        $this->seed(ProvinceSeeder::class);
        $this->seed(RegencySeeder::class);
        $count1 = Regency::count();

        $this->seed(RegencySeeder::class);
        $count2 = Regency::count();

        $this->assertEquals($count1, $count2);
    }
}
