<?php

namespace Ajangsupardi\PostcodeId\Console\Commands;

use Ajangsupardi\PostcodeId\Database\Seeders\PostcodeSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedPostcode extends Command
{
    protected $signature = 'postcode:seed';

    protected $description = 'Seed all postcode tables (provinces, regencies, districts, villages)';

    public function handle(): int
    {
        Artisan::call('db:seed', [
            '--class' => PostcodeSeeder::class,
            '--force' => true,
        ], $this->getOutput());

        $this->info('All postcode tables have been seeded successfully.');

        return self::SUCCESS;
    }
}
