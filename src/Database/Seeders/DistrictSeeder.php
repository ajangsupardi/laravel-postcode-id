<?php

namespace Ajangsupardi\PostcodeId\Database\Seeders;

use Ajangsupardi\PostcodeId\Services\PostcodeParser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $districtModel = Config::get('postcode.models.district');
        $regencyModel = Config::get('postcode.models.regency');

        if ($districtModel::count() > 0) {
            $this->command?->info('Districts already seeded. Skipping.');

            return;
        }

        $parser = app(PostcodeParser::class);
        $districtsByRegency = $parser->getDistricts();

        $regencyMap = $regencyModel::pluck('id', 'name')->toArray();
        $total = 0;

        foreach ($districtsByRegency as $regName => $districtNames) {
            if (! isset($regencyMap[$regName])) {
                continue;
            }

            $regencyId = $regencyMap[$regName];

            foreach ($districtNames as $name) {
                $districtModel::updateOrCreate(
                    ['name' => $name, 'regency_id' => $regencyId],
                    []
                );
                $total++;
            }
        }

        $this->command?->info('Seeded '.$total.' districts from postcode.');
    }
}
