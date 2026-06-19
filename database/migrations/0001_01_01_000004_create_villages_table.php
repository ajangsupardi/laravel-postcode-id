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
        Schema::create($this->table('villages'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained($this->table('districts'))->cascadeOnDelete();
            $table->string('name');
            $table->string('postal_code', 10)->nullable();
            $table->timestamps();

            $table->index('postal_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table('villages'));
    }
};
