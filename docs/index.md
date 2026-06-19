---
layout: default
title: Home
nav_order: 1
permalink: /
---

# Laravel Postcode ID
{: .fs-9 }

Laravel package for downloading and seeding Indonesian address data with postal codes.
{: .fs-6 .fw-300 }

[Get Started](/laravel-postcode-id/getting-started){: .btn .btn-primary .fs-5 .mb-4 .mb-md-0 .mr-2 }
[View on GitHub](https://github.com/ajangsupardi/laravel-postcode-id){: .btn .fs-5 .mb-4 .mb-md-0 }

---

## Overview

Laravel Postcode ID provides a complete solution for managing Indonesian administrative geography data. It downloads data directly from the official Pos Indonesia website and seeds your database with provinces, regencies, districts, and villages — including postal codes.

## Features

| Feature | Description |
|:--------|:------------|
| **Auto Download** | Fetches latest data from Pos Indonesia with retry & timeout handling |
| **Hierarchical Parsing** | Province → Regency → District → Village with name normalization |
| **Database Seeders** | Ready-to-use seeders with idempotent operations |
| **Migrations** | Auto-loaded, publishable migration files |
| **Configurable Models** | Extend default models or use your own |
| **Multi-version** | Supports Laravel 11, 12, and 13 |

## Quick Example

```php
use Ajangsupardi\PostcodeId\Models\Village;

// Find village by postal code with full hierarchy
$village = Village::with('district.regency.province')
    ->where('postal_code', '60111')
    ->first();

// Result: Gubeng, Kota Surabaya, Jawa Timur
```

## Data Coverage

{: .note }
**Requirements:** PHP ^8.3 and Laravel ^11.0 / ^12.0 / ^13.0

| Level | Count | Description |
|:------|------:|:------------|
| Provinces | 38 | All Indonesian provinces |
| Regencies | 500+ | Cities (Kota) and regencies (Kabupaten) |
| Districts | 7,000+ | Sub-districts (Kecamatan) |
| Villages | 85,000+ | Villages (Desa/Kelurahan) with postal codes |

{: .note }
Data count may vary as it is synced directly from Pos Indonesia.
