<?php

namespace Ajangsupardi\PostcodeId\Services;

class PostcodeParser
{
    private const DB_PROVINCE_NAMES = [
        'DAERAH ISTIMEWA YOGYAKARTA' => 'DI Yogyakarta',
        'DKI JAKARTA' => 'DKI Jakarta',
        'NUSA TENGGARA BARAT' => 'Nusa Tenggara Barat',
        'NUSA TENGGARA TIMUR' => 'Nusa Tenggara Timur',
        'KALIMANTAN BARAT' => 'Kalimantan Barat',
        'KALIMANTAN TENGAH' => 'Kalimantan Tengah',
        'KALIMANTAN SELATAN' => 'Kalimantan Selatan',
        'KALIMANTAN TIMUR' => 'Kalimantan Timur',
        'KALIMANTAN UTARA' => 'Kalimantan Utara',
        'SULAWESI UTARA' => 'Sulawesi Utara',
        'SULAWESI TENGAH' => 'Sulawesi Tengah',
        'SULAWESI SELATAN' => 'Sulawesi Selatan',
        'SULAWESI TENGGARA' => 'Sulawesi Tenggara',
        'SULAWESI BARAT' => 'Sulawesi Barat',
        'MALUKU UTARA' => 'Maluku Utara',
        'PAPUA BARAT' => 'Papua Barat',
        'PAPUA BARAT DAYA' => 'Papua Barat Daya',
        'PAPUA PEGUNUNGAN' => 'Papua Pegunungan',
        'PAPUA SELATAN' => 'Papua Selatan',
        'PAPUA TENGAH' => 'Papua Tengah',
        'KEPULAUAN BANGKA BELITUNG' => 'Kepulauan Bangka Belitung',
        'KEPULAUAN RIAU' => 'Kepulauan Riau',
    ];

    private const EXPECTED_CSV_HEADER = ['kodepos', 'desa', 'kecamatan', 'kab', 'provinsi'];

    private ?array $parsedData = null;

    public function parse(): array
    {
        if ($this->parsedData !== null) {
            return $this->parsedData;
        }

        $csvPath = config('postcode.storage_path').'/kodepos.csv';

        if (! file_exists($csvPath)) {
            throw new \RuntimeException("CSV file not found: {$csvPath}. Run 'php artisan postcode:download' first.");
        }

        $rows = $this->parseCsv($csvPath);

        $provinces = [];
        $regencies = [];
        $districts = [];
        $villages = [];

        $seenProvinces = [];
        $seenRegencies = [];
        $seenDistricts = [];

        foreach ($rows as $row) {
            $provUpper = strtoupper($row['provinsi']);
            $provName = self::DB_PROVINCE_NAMES[$provUpper] ?? $this->titleCase($row['provinsi']);
            if (! isset($seenProvinces[$provName])) {
                $seenProvinces[$provName] = true;
                $provinces[] = $provName;
            }

            $kabName = $this->parseRegencyName($row['kab']);
            if ($kabName !== '' && ! isset($seenRegencies[$kabName])) {
                $seenRegencies[$kabName] = true;
                $regencies[$provName][] = $kabName;
            }

            $districtName = $this->titleCase($row['kecamatan']);
            if ($districtName !== '' && ! isset($seenDistricts[$kabName.'|'.$districtName])) {
                $seenDistricts[$kabName.'|'.$districtName] = true;
                $districts[$kabName][] = $districtName;
            }

            $villageName = $this->titleCase($row['desa']);
            $postalCode = $row['kodepos'];
            if ($villageName !== '') {
                $villages[$kabName.'|'.$districtName][] = [
                    'name' => $villageName,
                    'postal_code' => $postalCode,
                ];
            }
        }

        $this->parsedData = [
            'provinces' => $provinces,
            'regencies' => $regencies,
            'districts' => $districts,
            'villages' => $villages,
        ];

        return $this->parsedData;
    }

    public function reset(): void
    {
        $this->parsedData = null;
    }

    public function getProvinces(): array
    {
        return $this->parse()['provinces'];
    }

    public function getRegencies(): array
    {
        return $this->parse()['regencies'];
    }

    public function getDistricts(): array
    {
        return $this->parse()['districts'];
    }

    public function getVillages(): array
    {
        return $this->parse()['villages'];
    }

    private function parseCsv(string $path): array
    {
        $handle = fopen($path, 'r');

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            throw new \RuntimeException("CSV file is empty: {$path}");
        }

        if ($header !== self::EXPECTED_CSV_HEADER) {
            fclose($handle);
            $actual = implode(', ', $header);
            $expected = implode(', ', self::EXPECTED_CSV_HEADER);

            throw new \RuntimeException("Unexpected CSV header. Expected [{$expected}], got [{$actual}]. The CSV format may have changed — re-run 'php artisan postcode:download'.");
        }

        $rows = [];
        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = array_combine($header, $data);
        }

        fclose($handle);

        return $rows;
    }

    private function parseRegencyName(string $kab): string
    {
        $kab = trim($kab);
        $upper = strtoupper($kab);

        if (str_starts_with($upper, 'KOTA ADM. ')) {
            $name = substr($kab, strlen('KOTA ADM. '));

            return 'Kota Administrasi '.$this->titleCase($name);
        }

        if (str_starts_with($upper, 'KOTA ')) {
            $name = substr($kab, strlen('KOTA '));

            return 'Kota '.$this->titleCase($name);
        }

        if (str_starts_with($upper, 'KAB. ')) {
            $name = substr($kab, strlen('KAB. '));

            return 'Kabupaten '.$this->titleCase($name);
        }

        return $this->titleCase($kab);
    }

    private function titleCase(string $str): string
    {
        return ucwords(strtolower(trim($str)));
    }
}
