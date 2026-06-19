<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function table(string $name): string
    {
        return (config('postcode.table_prefix') ?? '').$name;
    }

    public function up(): void
    {
        Schema::create($this->table('provinces'), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table('provinces'));
    }
};
