<?php

namespace Ajangsupardi\PostcodeId\Tests\Feature;

use Ajangsupardi\PostcodeId\Console\Commands\DownloadPostcode;
use Ajangsupardi\PostcodeId\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class DownloadPostcodeTest extends TestCase
{
    public function test_downloads_and_saves_csv(): void
    {
        $html = $this->sampleHtml([
            [1, '1234567890', 'Desa Test', 'Kec Test', 'Kab. Test', 'ACEH'],
            [2, '1234567891', 'Desa Test2', 'Kec Test', 'Kab. Test', 'ACEH'],
        ]);

        Http::fake([
            'kodepos.posindonesia.co.id/*' => Http::response($html, 200),
        ]);

        $this->artisan(DownloadPostcode::class)
            ->expectsOutputToContain('Downloading postcode data')
            ->expectsOutputToContain('Total rows: 2')
            ->assertExitCode(0);

        $storagePath = config('postcode.storage_path');
        $this->assertFileExists($storagePath.'/kodepos.csv');

        $csv = file_get_contents($storagePath.'/kodepos.csv');
        $this->assertStringContainsString('1234567890', $csv);
        $this->assertStringContainsString('Desa Test', $csv);
    }

    public function test_handles_empty_html_response(): void
    {
        Http::fake([
            'kodepos.posindonesia.co.id/*' => Http::response('<html><body></body></html>', 200),
        ]);

        $this->artisan(DownloadPostcode::class)
            ->expectsOutputToContain('No data rows extracted')
            ->assertExitCode(1);
    }

    public function test_handles_http_failure(): void
    {
        Http::fake([
            'kodepos.posindonesia.co.id/*' => Http::response('', 500),
        ]);

        $this->artisan(DownloadPostcode::class)
            ->expectsOutputToContain('Failed to download postcode data')
            ->assertExitCode(1);
    }

    public function test_skips_incomplete_rows(): void
    {
        $html = $this->sampleHtml([
            [1, '1234567890', 'Desa Test', 'Kec Test', 'Kab. Test', 'ACEH'],
            [2, '', '', '', '', ''],
        ]);

        Http::fake([
            'kodepos.posindonesia.co.id/*' => Http::response($html, 200),
        ]);

        $this->artisan(DownloadPostcode::class)
            ->expectsOutputToContain('Total rows: 1')
            ->assertExitCode(0);
    }

    private function sampleHtml(array $rows): string
    {
        $cells = '';
        foreach ($rows as $row) {
            foreach ($row as $cell) {
                $cells .= '<td>'.$cell.'</td>';
            }
        }

        return '<html><body><table>'.$cells.'</table></body></html>';
    }
}
