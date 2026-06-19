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
        Schema::create($this->table('districts'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('regency_id')->constrained($this->table('regencies'))->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table('districts'));
    }
};
