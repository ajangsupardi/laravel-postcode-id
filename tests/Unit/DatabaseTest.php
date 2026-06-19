<?php

namespace Ajangsupardi\PostcodeId\Tests\Unit;

use Ajangsupardi\PostcodeId\Tests\TestCase;
use Illuminate\Support\Facades\Schema;

class DatabaseTest extends TestCase
{
    public function test_migrations_create_all_tables(): void
    {
        $this->assertTrue(Schema::hasTable('provinces'));
        $this->assertTrue(Schema::hasTable('regencies'));
        $this->assertTrue(Schema::hasTable('districts'));
        $this->assertTrue(Schema::hasTable('villages'));
    }

    public function test_provinces_table_structure(): void
    {
        $this->assertTrue(Schema::hasColumn('provinces', 'id'));
        $this->assertTrue(Schema::hasColumn('provinces', 'name'));
        $this->assertTrue(Schema::hasColumn('provinces', 'code'));
        $this->assertTrue(Schema::hasColumn('provinces', 'created_at'));
        $this->assertTrue(Schema::hasColumn('provinces', 'updated_at'));
    }

    public function test_regencies_table_has_foreign_key(): void
    {
        $this->assertTrue(Schema::hasColumn('regencies', 'province_id'));
        $this->assertTrue(Schema::hasColumn('regencies', 'name'));
    }

    public function test_districts_table_has_foreign_key(): void
    {
        $this->assertTrue(Schema::hasColumn('districts', 'regency_id'));
        $this->assertTrue(Schema::hasColumn('districts', 'name'));
    }

    public function test_villages_table_has_postal_code(): void
    {
        $this->assertTrue(Schema::hasColumn('villages', 'district_id'));
        $this->assertTrue(Schema::hasColumn('villages', 'name'));
        $this->assertTrue(Schema::hasColumn('villages', 'postal_code'));
    }
}
