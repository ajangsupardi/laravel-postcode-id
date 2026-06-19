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
        Schema::create($this->table('regencies'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained($this->table('provinces'))->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table('regencies'));
    }
};
