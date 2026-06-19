<?php

namespace Ajangsupardi\PostcodeId\Database\Seeders;

use Illuminate\Database\Seeder;

class PostcodeSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProvinceSeeder::class,
            RegencySeeder::class,
            DistrictSeeder::class,
            VillageSeeder::class,
        ]);
    }
}
