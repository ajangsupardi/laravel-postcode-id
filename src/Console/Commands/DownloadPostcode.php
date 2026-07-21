<?php

namespace Ajangsupardi\PostcodeId\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class DownloadPostcode extends Command
{
    protected $signature = 'postcode:download';

    protected $description = 'Download all postcode data from Pos Indonesia and save as CSV';

    private const EXPECTED_COLUMNS = 6;

    private const CSV_HEADER = ['kodepos', 'desa', 'kecamatan', 'kab', 'provinsi'];

    public function handle(): int
    {
        $this->info('Downloading postcode data from Pos Indonesia...');

        $response = $this->fetchData();

        if ($response === null) {
            return self::FAILURE;
        }

        $rows = $this->parseHtml($response);

        if ($rows === []) {
            $this->error('No data rows extracted from the response. The HTML structure may have changed.');

            return self::FAILURE;
        }

        $this->saveCsv($rows);

        $this->info("Downloaded and saved ".config('postcode.storage_path').'/kodepos.csv');
        $this->info('Total rows: '.count($rows));
        $this->info('Unique provinsi: '.count(array_unique(array_column($rows, 'provinsi'))));
        $this->info('Unique kab: '.count(array_unique(array_column($rows, 'kab'))));
        $this->info('Unique kecamatan: '.count(array_unique(array_column($rows, 'kecamatan'))));
        $this->info('Unique desa: '.count(array_unique(array_column($rows, 'desa'))));

        return self::SUCCESS;
    }

    private function fetchData(): ?string
    {
        $config = config('postcode.http');

        try {
            $response = Http::timeout($config['timeout'])
                ->connectTimeout($config['connect_timeout'])
                ->retry($config['retry'], $config['retry_delay'], function (\Exception $exception) {
                    return $exception instanceof ConnectionException;
                })
                ->withHeaders([
                    'User-Agent' => $config['user_agent'],
                ])
                ->asForm()
                ->post('https://kodepos.posindonesia.co.id/CariKodepos', [
                    'kodepos' => ' ',
                ]);
        } catch (\Exception $e) {
            $this->error('Failed to download postcode data: '.$e->getMessage());

            return null;
        }

        if ($response->failed()) {
            $this->error('Failed to download postcode data. HTTP status: '.$response->status());

            return null;
        }

        return $response->body();
    }

    private function parseHtml(string $html): array
    {
        preg_match_all('/<td>([^<]+)<\/td>/', $html, $matches);
        $cells = $matches[1];

        $expectedCount = count($cells) - (count($cells) % self::EXPECTED_COLUMNS);
        if ($expectedCount === 0 && $cells !== []) {
            $this->warn('HTML contains cells but none form a complete row of '.self::EXPECTED_COLUMNS.' columns.');
        }

        $rows = [];
        for ($i = 0; $i < $expectedCount; $i += self::EXPECTED_COLUMNS) {
            $row = [
                'kodepos' => trim($cells[$i + 1]),
                'desa' => trim($cells[$i + 2]),
                'kecamatan' => trim($cells[$i + 3]),
                'kab' => trim($cells[$i + 4]),
                'provinsi' => trim($cells[$i + 5]),
            ];

            if ($row['kodepos'] === '' || $row['provinsi'] === '') {
                continue;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    private function saveCsv(array $rows): void
    {
        $storagePath = config('postcode.storage_path');

        if (! is_dir($storagePath) && ! mkdir($storagePath, 0755, true) && ! is_dir($storagePath)) {
            throw new \RuntimeException("Failed to create storage directory: {$storagePath}");
        }

        $csvPath = $storagePath.'/kodepos.csv';
        $handle = fopen($csvPath, 'w');

        if ($handle === false) {
            throw new \RuntimeException("Failed to open file for writing: {$csvPath}");
        }

        fputcsv($handle, self::CSV_HEADER);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
    }
}
