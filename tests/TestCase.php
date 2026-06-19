<?php

namespace Ajangsupardi\PostcodeId\Tests;

use Ajangsupardi\PostcodeId\PostcodeIdServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            PostcodeIdServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('postcode.storage_path', sys_get_temp_dir().'/postcode_test_'.uniqid());
        $app['config']->set('postcode.table_prefix', null);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpFilesystem();
    }

    protected function tearDown(): void
    {
        $this->cleanFilesystem();

        parent::tearDown();
    }

    private function setUpFilesystem(): void
    {
        $path = config('postcode.storage_path');

        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    private function cleanFilesystem(): void
    {
        $path = config('postcode.storage_path');

        if (is_dir($path)) {
            $files = glob($path.'/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($path);
        }
    }

    protected function seedSampleCsv(?string $csvContent = null): string
    {
        $storagePath = config('postcode.storage_path');
        $csvPath = $storagePath.'/kodepos.csv';

        $csvContent ??= <<<'CSV'
kodepos,desa,kecamatan,kab,provinsi
1234567890,Desa Test,Kecamatan Test,Kab. Test,ACEH
1234567891,Desa Test2,Kecamatan Test,Kab. Test,ACEH
1234567892,Desa Test3,Kecamatan Test2,Kab. Test2,DKI JAKARTA
CSV;

        file_put_contents($csvPath, $csvContent);

        return $csvPath;
    }
}
