<?php

namespace Ajangsupardi\PostcodeId\Database\Seeders;

use Ajangsupardi\PostcodeId\Services\PostcodeParser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class VillageSeeder extends Seeder
{
    public function run(): void
    {
        $villageModel = Config::get('postcode.models.village');
        $districtModel = Config::get('postcode.models.district');
        $regencyModel = Config::get('postcode.models.regency');

        if ($villageModel::count() > 0) {
            $this->command?->info('Villages already seeded. Skipping.');

            return;
        }

        $parser = app(PostcodeParser::class);
        $villagesByDistrict = $parser->getVillages();

        $regencyMap = $regencyModel::pluck('id', 'name')->toArray();

        $allDistricts = $districtModel::select('id', 'name', 'regency_id')->get()
            ->keyBy(fn ($d) => $d->regency_id.'|'.$d->name);

        $total = 0;
        $chunk = [];
        $chunkSize = 1000;
        $tableName = app($villageModel)->getTable();
        $isPostgres = DB::getDriverName() === 'pgsql';

        if ($isPostgres) {
            DB::statement('ALTER TABLE '.$tableName.' DISABLE TRIGGER ALL');
        }

        try {
            foreach ($villagesByDistrict as $key => $villageData) {
                $parts = explode('|', $key);
                $regName = $parts[0] ?? '';
                $distName = $parts[1] ?? '';

                if (! isset($regencyMap[$regName])) {
                    continue;
                }

                $districtKey = $regencyMap[$regName].'|'.$distName;
                $district = $allDistricts[$districtKey] ?? null;

                if (! $district) {
                    continue;
                }

                foreach ($villageData as $village) {
                    $chunk[] = [
                        'district_id' => $district->id,
                        'name' => $village['name'],
                        'postal_code' => $village['postal_code'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $total++;

                    if (count($chunk) >= $chunkSize) {
                        $villageModel::insert($chunk);
                        $chunk = [];
                    }
                }
            }

            if ($chunk !== []) {
                $villageModel::insert($chunk);
            }
        } finally {
            if ($isPostgres) {
                DB::statement('ALTER TABLE '.$tableName.' ENABLE TRIGGER ALL');
            }
        }

        $this->command?->info('Seeded '.$total.' villages from postcode.');
    }
}
