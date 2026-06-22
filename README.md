# Laravel Postcode ID

[![Latest Stable Version](https://poser.pugx.org/ajangsupardi/laravel-postcode-id/v/stable)](https://packagist.org/packages/ajangsupardi/laravel-postcode-id)
[![License](https://poser.pugx.org/ajangsupardi/laravel-postcode-id/license)](https://packagist.org/packages/ajangsupardi/laravel-postcode-id)
[![Total Downloads](https://poser.pugx.org/ajangsupardi/laravel-postcode-id/downloads)](https://packagist.org/packages/ajangsupardi/laravel-postcode-id)

Laravel package for Indonesian address data with postal codes — downloaded fresh from Pos Indonesia.

## Features

- **Postal code lookup** — Query villages by postal code with full address hierarchy
- **Fresh data** — Downloads directly from [Pos Indonesia](https://kodepos.posindonesia.co.id), not static dumps
- **Idempotent seeders** — Safe to run multiple times without duplicates
- **Hierarchical parsing** — Province → Regency → District → Village with name normalization
- **Custom models** — Extend default models or use your own
- **Laravel 11, 12, 13** — Modern PHP 8.3+

## Installation

```bash
composer require ajangsupardi/laravel-postcode-id
```

### Publish Configuration (optional)

```bash
php artisan vendor:publish --tag=postcode-config
```

### Publish Migrations (optional)

```bash
php artisan vendor:publish --tag=postcode-migrations
```

> **Note:** Migrations are automatically loaded by the service provider, so publishing is only needed if you want to modify the table structure.

## Usage

### 1. Download Postcode Data

```bash
php artisan postcode:download
```

This command will download all postcode data from Pos Indonesia and save it as a CSV file.

### 2. Run Migrations & Seeder

```bash
php artisan migrate
php artisan postcode:seed
```

Or add it to your `DatabaseSeeder.php`:

```php
use Ajangsupardi\PostcodeId\Database\Seeders\PostcodeSeeder;

public function run(): void
{
    $this->call([
        PostcodeSeeder::class,
    ]);
}
```

### 3. Auto-download & Seed

If you want to combine download and seed in a single step, you can call the download command before the seeder:

```php
use Illuminate\Support\Facades\Artisan;

$storagePath = config('postcode.storage_path');
if (! file_exists($storagePath.'/kodepos.csv')) {
    Artisan::call('postcode:download');
}
```

## Postal Code Lookup

The main feature — find any Indonesian address by postal code:

```php
use Ajangsupardi\PostcodeId\Models\Village;

// Village by postal code
$village = Village::where('postal_code', '60111')->first();

// With full address hierarchy
$village = Village::with('district.regency.province')
    ->where('postal_code', '60111')
    ->first();

// Result: Gubeng → Kota Surabaya → Jawa Timur
```

```php
// Search by village name
$villages = Village::where('name', 'LIKE', '%Gubeng%')
    ->with('district.regency')
    ->get();
```

## Database Tables

This package creates 4 tables:

| Table | Description |
|-------|-------------|
| `provinces` | Province data (id, name, code) |
| `regencies` | Regency/city data (id, province_id, name) |
| `districts` | District/sub-district data (id, regency_id, name) |
| `villages` | Village data (id, district_id, name, postal_code) |

## Configuration

Edit `config/postcode.php`:

```php
return [
    // CSV file storage path
    'storage_path' => storage_path('app/postcode'),

    // Database table prefix (null = no prefix)
    'table_prefix' => null,

    // Custom models
    'models' => [
        'province' => Ajangsupardi\PostcodeId\Models\Province::class,
        'regency'  => Ajangsupardi\PostcodeId\Models\Regency::class,
        'district' => Ajangsupardi\PostcodeId\Models\District::class,
        'village'  => Ajangsupardi\PostcodeId\Models\Village::class,
    ],

    // HTTP client settings
    'http' => [
        'timeout' => 60,
        'connect_timeout' => 10,
        'retry' => 3,
        'retry_delay' => 1000,
        'user_agent' => 'Mozilla/5.0 (compatible; LaravelPostcodeId/1.0)',
    ],
];
```

### Custom Models

You can extend the default models to add columns or relationships:

```php
namespace App\Models;

use Ajangsupardi\PostcodeId\Models\Province as BaseProvince;

class Province extends BaseProvince
{
    protected $fillable = ['name', 'code', 'your_custom_field'];

    // Add custom relationships or methods
}
```

Then update the configuration:

```php
'models' => [
    'province' => App\Models\Province::class,
],
```

### Table Prefix

If you want to use a prefix for your tables:

```php
'table_prefix' => 'postcode_',
```

This will create tables: `postcode_provinces`, `postcode_regencies`, `postcode_districts`, `postcode_villages`.

## Example Queries

```php
use Ajangsupardi\PostcodeId\Models\Province;
use Ajangsupardi\PostcodeId\Models\Village;

// Get all provinces
$provinces = Province::all();

// Get regencies in East Java
$regencies = Province::where('name', 'Jawa Timur')
    ->first()
    ->regencies;

// Find a village by postal code
$village = Village::where('postal_code', '60111')->first();

// Get full address hierarchy from village to province
$village = Village::with('district.regency.province')
    ->where('postal_code', '60111')
    ->first();
```

## Requirements

- PHP ^8.3
- Laravel ^11.0 / ^12.0 / ^13.0

## Changelog

### v1.1.0
- Added `postcode:seed` artisan command to fix shell escaping bug on Linux

### v1.0.0
- Initial release — download, parse, and seed Indonesian address data with postal codes

## License

MIT License. See [LICENSE](LICENSE) for more information.
