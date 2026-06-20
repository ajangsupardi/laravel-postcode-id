---
layout: default
title: Getting Started
nav_order: 2
---

# Getting Started
{: .no_toc }

## Table of contents
{: .no_toc .text-delta }

1. TOC
{:toc}

---

## Installation

{: .tip }
**Requirements:** PHP ^8.3 and Laravel ^11.0 / ^12.0 / ^13.0

Install the package via Composer:

```bash
composer require ajangsupardi/laravel-postcode-id
```

{: .tip }
The service provider is auto-discovered. No manual registration needed.

## Publish Assets

### Configuration (optional)

```bash
php artisan vendor:publish --tag=postcode-config
```

This creates `config/postcode.php` in your project root.

### Migrations (optional)

```bash
php artisan vendor:publish --tag=postcode-migrations
```

{: .note }
Migrations are automatically loaded by the service provider. Publishing is only needed if you want to customize the table structure.

## Database Setup

Run migrations to create the required tables:

```bash
php artisan migrate
```

This creates 4 tables:

| Table | Description |
|:------|:------------|
| `provinces` | Province data with ISO codes |
| `regencies` | Cities and regencies with province foreign key |
| `districts` | Sub-districts with regency foreign key |
| `villages` | Villages with postal codes and district foreign key |

## Download Data

Download all postcode data from Pos Indonesia:

```bash
php artisan postcode:download
```

This fetches ~85,000 records and saves them as a CSV file in your storage directory.

{: .warning }
This command makes an HTTP request to the Pos Indonesia server. Ensure your server has internet access.

## Seed Database

Run the seeder to populate your database:

```bash
php artisan postcode:seed
```

Or add to your `DatabaseSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ajangsupardi\PostcodeId\Database\Seeders\PostcodeSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PostcodeSeeder::class,
        ]);
    }
}
```

## Combined Download & Seed

For a streamlined setup, combine download and seed:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Ajangsupardi\PostcodeId\Database\Seeders\PostcodeSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $storagePath = config('postcode.storage_path');

        if (!file_exists($storagePath . '/kodepos.csv')) {
            Artisan::call('postcode:download');
        }

        $this->call([
            PostcodeSeeder::class,
        ]);
    }
}
```

## What's Next?

- [Configuration](/laravel-postcode-id/configuration) - Customize package behavior
- [API Reference](/laravel-postcode-id/api) - Models, commands, and query examples
