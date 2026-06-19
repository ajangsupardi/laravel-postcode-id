<?php

namespace Ajangsupardi\PostcodeId\Tests\Unit;

use Ajangsupardi\PostcodeId\Services\PostcodeParser;
use Ajangsupardi\PostcodeId\Tests\TestCase;

class PostcodeParserTest extends TestCase
{
    public function test_throws_when_csv_not_found(): void
    {
        $this->app['config']->set('postcode.storage_path', sys_get_temp_dir().'/nonexistent_'.uniqid());

        $parser = new PostcodeParser;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/CSV file not found/');

        $parser->parse();
    }

    public function test_throws_on_invalid_csv_header(): void
    {
        $this->seedSampleCsv("wrong,header,here,columns,data\n1,2,3,4,5\n");

        $parser = new PostcodeParser;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Unexpected CSV header/');

        $parser->parse();
    }

    public function test_throws_on_empty_csv(): void
    {
        $this->seedSampleCsv('');

        $parser = new PostcodeParser;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/CSV file is empty/');

        $parser->parse();
    }

    public function test_parses_provinces_correctly(): void
    {
        $this->seedSampleCsv();

        $parser = new PostcodeParser;
        $provinces = $parser->getProvinces();

        $this->assertContains('Aceh', $provinces);
        $this->assertContains('DKI Jakarta', $provinces);
        $this->assertCount(2, $provinces);
    }

    public function test_parses_regencies_with_correct_prefix(): void
    {
        $this->seedSampleCsv();

        $parser = new PostcodeParser;
        $regencies = $parser->getRegencies();

        $this->assertArrayHasKey('Aceh', $regencies);
        $this->assertContains('Kabupaten Test', $regencies['Aceh']);

        $this->assertArrayHasKey('DKI Jakarta', $regencies);
        $this->assertContains('Kabupaten Test2', $regencies['DKI Jakarta']);
    }

    public function test_parses_regency_kota_prefix(): void
    {
        $csv = <<<'CSV'
kodepos,desa,kecamatan,kab,provinsi
1234567890,Desa Test,Kec Test,KOTA BANDUNG,JAWA BARAT
CSV;

        $this->seedSampleCsv($csv);

        $parser = new PostcodeParser;
        $regencies = $parser->getRegencies();

        $this->assertContains('Kota Bandung', $regencies['Jawa Barat']);
    }

    public function test_parses_regency_kota_admin_prefix(): void
    {
        $csv = <<<'CSV'
kodepos,desa,kecamatan,kab,provinsi
1234567890,Desa Test,Kec Test,KOTA ADM. JAKARTA PUSAT,DKI JAKARTA
CSV;

        $this->seedSampleCsv($csv);

        $parser = new PostcodeParser;
        $regencies = $parser->getRegencies();

        $this->assertContains('Kota Administrasi Jakarta Pusat', $regencies['DKI Jakarta']);
    }

    public function test_parses_regency_kab_prefix(): void
    {
        $csv = <<<'CSV'
kodepos,desa,kecamatan,kab,provinsi
1234567890,Desa Test,Kec Test,KAB. BOGOR,JAWA BARAT
CSV;

        $this->seedSampleCsv($csv);

        $parser = new PostcodeParser;
        $regencies = $parser->getRegencies();

        $this->assertContains('Kabupaten Bogor', $regencies['Jawa Barat']);
    }

    public function test_parses_villages_with_postal_code(): void
    {
        $this->seedSampleCsv();

        $parser = new PostcodeParser;
        $villages = $parser->getVillages();

        $this->assertArrayHasKey('Kabupaten Test|Kecamatan Test', $villages);
        $this->assertEquals([
            ['name' => 'Desa Test', 'postal_code' => '1234567890'],
            ['name' => 'Desa Test2', 'postal_code' => '1234567891'],
        ], $villages['Kabupaten Test|Kecamatan Test']);
    }

    public function test_province_name_normalization(): void
    {
        $csv = <<<'CSV'
kodepos,desa,kecamatan,kab,provinsi
1234567890,Desa Test,Kec Test,Kab. Test,DAERAH ISTIMEWA YOGYAKARTA
1234567891,Desa Test,Kec Test,Kab. Test,KEPULAUAN BANGKA BELITUNG
1234567892,Desa Test,Kec Test,Kab. Test,NUSA TENGGARA BARAT
CSV;

        $this->seedSampleCsv($csv);

        $parser = new PostcodeParser;
        $provinces = $parser->getProvinces();

        $this->assertContains('DI Yogyakarta', $provinces);
        $this->assertContains('Kepulauan Bangka Belitung', $provinces);
        $this->assertContains('Nusa Tenggara Barat', $provinces);
    }

    public function test_cache_can_be_reset(): void
    {
        $this->seedSampleCsv();

        $parser = new PostcodeParser;
        $first = $parser->parse();

        $this->assertSame($first, $parser->parse());

        $parser->reset();

        $this->seedSampleCsv("kodepos,desa,kecamatan,kab,provinsi\n9999999999,New Village,Kec New,Kab. New,BALI\n");

        $second = $parser->parse();

        $this->assertContains('Bali', $second['provinces']);
        $this->assertNotContains('Aceh', $second['provinces']);
    }

    public function test_get_provinces_returns_same_as_parse(): void
    {
        $this->seedSampleCsv();

        $parser = new PostcodeParser;

        $this->assertSame($parser->parse()['provinces'], $parser->getProvinces());
    }

    public function test_empty_village_name_is_skipped(): void
    {
        $csv = <<<'CSV'
kodepos,desa,kecamatan,kab,provinsi
1234567890,,Kec Test,Kab. Test,ACEH
1234567892,Desa Good,Kec Good,Kab Good,ACEH
CSV;

        $this->seedSampleCsv($csv);

        $parser = new PostcodeParser;
        $data = $parser->parse();

        $this->assertArrayNotHasKey('Kabupaten Test|Kec Test', $data['villages']);
        $this->assertEquals('Desa Good', $data['villages']['Kab Good|Kec Good'][0]['name']);
    }

    public function test_empty_kecamatan_name_creates_unkeyed_village(): void
    {
        $csv = <<<'CSV'
kodepos,desa,kecamatan,kab,provinsi
1234567891,Desa Test,,Kab. Test,ACEH
1234567892,Desa Good,Kec Good,Kab Good,ACEH
CSV;

        $this->seedSampleCsv($csv);

        $parser = new PostcodeParser;
        $data = $parser->parse();

        $this->assertArrayHasKey('Kabupaten Test|', $data['villages']);
        $this->assertEquals('Desa Test', $data['villages']['Kabupaten Test|'][0]['name']);
        $this->assertEquals('Desa Good', $data['villages']['Kab Good|Kec Good'][0]['name']);
    }
}
