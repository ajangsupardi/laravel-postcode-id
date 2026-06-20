---
layout: default
title: API Reference
nav_order: 4
---

# API Reference
{: .no_toc }

## Table of contents
{: .no_toc .text-delta }

1. TOC
{:toc}

---

## Artisan Commands

### `postcode:download`

Downloads all postcode data from Pos Indonesia and saves as CSV.

```bash
php artisan postcode:download
```

**Options:** None

**Output:** CSV file at the configured `storage_path` (default: `storage/app/postcode/kodepos.csv`)

---

### `postcode:seed`

Seeds all postcode tables (provinces, regencies, districts, villages) from the downloaded CSV.

```bash
php artisan postcode:seed
```

**Options:** None

**Output:** All 4 tables populated with data from Pos Indonesia

{: .tip }
This command is a convenient alternative to `php artisan db:seed --class=...`, which can fail on Linux due to shell backslash escaping.

---

## Models

### Province

```php
use Ajangsupardi\PostcodeId\Models\Province;

// Get all provinces
$provinces = Province::all();

// Find by code
$province = Province::where('code', 'JI')->first();

// Get regencies
$regencies = $province->regencies;
```

**Table:** `provinces`

| Column | Type | Description |
|:-------|:-----|:------------|
| `id` | bigint | Primary key |
| `name` | string | Province name (e.g., "Jawa Timur") |
| `code` | string(10) | Province code (e.g., "JI") |

**Relationships:**

| Method | Type | Related Model |
|:-------|:-----|:--------------|
| `regencies()` | HasMany | Regency |

---

### Regency

```php
use Ajangsupardi\PostcodeId\Models\Regency;

// Get regencies for a province
$regencies = Regency::where('province_id', $provinceId)->get();

// Get parent province
$province = $regency->province;

// Get districts
$districts = $regency->districts;
```

**Table:** `regencies`

| Column | Type | Description |
|:-------|:-----|:------------|
| `id` | bigint | Primary key |
| `province_id` | bigint | Foreign key to `provinces` |
| `name` | string | Regency/city name (e.g., "Kota Surabaya") |

**Relationships:**

| Method | Type | Related Model |
|:-------|:-----|:--------------|
| `province()` | BelongsTo | Province |
| `districts()` | HasMany | District |

---

### District

```php
use Ajangsupardi\PostcodeId\Models\District;

// Get districts for a regency
$districts = District::where('regency_id', $regencyId)->get();

// Get parent regency
$regency = $district->regency;

// Get villages
$villages = $district->villages;
```

**Table:** `districts`

| Column | Type | Description |
|:-------|:-----|:------------|
| `id` | bigint | Primary key |
| `regency_id` | bigint | Foreign key to `regencies` |
| `name` | string | District name (e.g., "Gubeng") |

**Relationships:**

| Method | Type | Related Model |
|:-------|:-----|:--------------|
| `regency()` | BelongsTo | Regency |
| `villages()` | HasMany | Village |

---

### Village

```php
use Ajangsupardi\PostcodeId\Models\Village;

// Find by postal code
$village = Village::where('postal_code', '60111')->first();

// Get with full hierarchy
$village = Village::with('district.regency.province')
    ->where('postal_code', '60111')
    ->first();

// Search by name
$villages = Village::where('name', 'LIKE', '%Gubeng%')->get();
```

**Table:** `villages`

| Column | Type | Description |
|:-------|:-----|:------------|
| `id` | bigint | Primary key |
| `district_id` | bigint | Foreign key to `districts` |
| `name` | string | Village name (e.g., "Gubeng") |
| `postal_code` | string(10) | Postal code (nullable) |

**Relationships:**

| Method | Type | Related Model |
|:-------|:-----|:--------------|
| `district()` | BelongsTo | District |

**Scopes:**

| Scope | Parameters | Description |
|:------|:-----------|:------------|
| `postalCode` | `$query, $code` | Filter by postal code |

---

## Seeders

### PostcodeSeeder

Runs all seeders in the correct order (Province → Regency → District → Village).

```php
use Ajangsupardi\PostcodeId\Database\Seeders\PostcodeSeeder;

Artisan::call('db:seed', ['--class' => PostcodeSeeder::class]);
```

### Individual Seeders

Run specific seeders if needed:

```php
use Ajangsupardi\PostcodeId\Database\Seeders\ProvinceSeeder;
use Ajangsupardi\PostcodeId\Database\Seeders\RegencySeeder;
use Ajangsupardi\PostcodeId\Database\Seeders\DistrictSeeder;
use Ajangsupardi\PostcodeId\Database\Seeders\VillageSeeder;

Artisan::call('db:seed', ['--class' => ProvinceSeeder::class]);
Artisan::call('db:seed', ['--class' => RegencySeeder::class]);
Artisan::call('db:seed', ['--class' => DistrictSeeder::class]);
Artisan::call('db:seed', ['--class' => VillageSeeder::class]);
```

{: .warning }
Seeders must run in order: Province → Regency → District → Village (due to foreign key dependencies).

---

## Example Queries

### Full Address Lookup

```php
$village = Village::with('district.regency.province')
    ->where('postal_code', '60111')
    ->first();

// Output:
// {
//   "name": "Gubeng",
//   "postal_code": "60111",
//   "district": {
//     "name": "Gubeng",
//     "regency": {
//       "name": "Kota Surabaya",
//       "province": {
//         "name": "Jawa Timur",
//         "code": "JI"
//       }
//     }
//   }
// }
```

### All Districts in a Province

```php
use Ajangsupardi\PostcodeId\Models\District;

$districts = District::whereHas('regency', function ($query) use ($provinceId) {
    $query->where('province_id', $provinceId);
})->get();
```

### Search Villages by Name

```php
$villages = Village::where('name', 'LIKE', '%Gubeng%')
    ->with('district.regency')
    ->get();
```

### Count Statistics

```php
use Ajangsupardi\PostcodeId\Models\{Province, Regency, District, Village};

$stats = [
    'provinces' => Province::count(),
    'regencies' => Regency::count(),
    'districts' => District::count(),
    'villages'  => Village::count(),
];
```

### Find by Province Code

```php
use Ajangsupardi\PostcodeId\Models\Province;

$regencies = Province::where('code', 'JI')  // Jawa Timur
    ->first()
    ->regencies()
    ->with('districts')
    ->get();
```
