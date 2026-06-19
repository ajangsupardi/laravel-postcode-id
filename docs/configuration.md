---
layout: default
title: Configuration
nav_order: 3
---

# Configuration
{: .no_toc }

## Table of contents
{: .no_toc .text-delta }

1. TOC
{:toc}

---

After publishing the config file, customize the package behavior in `config/postcode.php`.

## Full Configuration

```php
<?php

return [
    // CSV file storage path
    'storage_path' => storage_path('app/postcode'),

    // Database table prefix (null = no prefix)
    'table_prefix' => null,

    // Custom Eloquent models
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

---

## Storage Path

Customize where the downloaded CSV file is stored:

```php
'storage_path' => storage_path('app/postcode'),
```

{: .tip }
Use an absolute path or `storage_path()` helper for reliability.

---

## Table Prefix

Use a prefix to avoid table name conflicts with other packages:

```php
'table_prefix' => 'postcode_',
```

This creates tables: `postcode_provinces`, `postcode_regencies`, `postcode_districts`, `postcode_villages`.

{: .note }
If you change the prefix after seeding, you'll need to re-run migrations and seeders.

---

## Custom Models

Extend the default models to add columns, relationships, or custom logic.

### Example: Adding a Column

```php
<?php

namespace App\Models;

use Ajangsupardi\PostcodeId\Models\Province as BaseProvince;

class Province extends BaseProvince
{
    protected $fillable = ['name', 'code', 'population'];

    public function stats()
    {
        return $this->hasOne(ProvinceStat::class);
    }
}
```

### Update Configuration

```php
'models' => [
    'province' => App\Models\Province::class,
],
```

{: .warning }
If you add new columns, create a migration to add them to the database table.

---

## HTTP Settings

Configure the HTTP client for downloading data from Pos Indonesia:

| Setting | Type | Default | Description |
|:--------|:-----|:--------|:------------|
| `timeout` | int | 60 | Request timeout in seconds |
| `connect_timeout` | int | 10 | Connection timeout in seconds |
| `retry` | int | 3 | Number of retry attempts |
| `retry_delay` | int | 1000 | Delay between retries in milliseconds |
| `user_agent` | string | `Mozilla/5.0...` | Custom User-Agent string |

### Increase Timeouts

If you experience connection issues:

```php
'http' => [
    'timeout' => 120,
    'connect_timeout' => 30,
],
```

### Custom User-Agent

Some servers block generic user agents:

```php
'http' => [
    'user_agent' => 'YourApp/1.0 (your@email.com)',
],
```
