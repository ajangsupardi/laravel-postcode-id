<?php

namespace Ajangsupardi\PostcodeId\Console\Commands;

use Ajangsupardi\PostcodeId\Database\Seeders\PostcodeSeeder;
use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Artisan;

class SeedPostcode extends Command
{
    protected $signature = 'postcode:seed';

    protected $description = 'Seed all postcode tables (provinces, regencies, districts, villages)';

    public function handle(DatabaseManager $db): int
    {
        $db->beginTransaction();

        try {
            Artisan::call('db:seed', [
                '--class' => PostcodeSeeder::class,
                '--force' => true,
            ]);

            $this->info(Artisan::output());

            $db->commit();

            $this->info('All postcode tables have been seeded successfully.');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $db->rollBack();

            $this->error('Seeding failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
