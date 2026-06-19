<?php

namespace Ajangsupardi\PostcodeId\Database\Seeders;

use Ajangsupardi\PostcodeId\Services\PostcodeParser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class RegencySeeder extends Seeder
{
    public function run(): void
    {
        $regencyModel = Config::get('postcode.models.regency');
        $provinceModel = Config::get('postcode.models.province');

        if ($regencyModel::count() > 0) {
            $this->command?->info('Regencies already seeded. Skipping.');

            return;
        }

        $parser = app(PostcodeParser::class);
        $regenciesByProvince = $parser->getRegencies();

        $provinceMap = $provinceModel::pluck('id', 'name')->toArray();
        $total = 0;

        foreach ($regenciesByProvince as $provName => $regencyNames) {
            if (! isset($provinceMap[$provName])) {
                continue;
            }

            $provinceId = $provinceMap[$provName];

            foreach ($regencyNames as $name) {
                $regencyModel::updateOrCreate(
                    ['name' => $name, 'province_id' => $provinceId],
                    []
                );
                $total++;
            }
        }

        $this->command?->info('Seeded '.$total.' regencies from postcode.');
    }
}
