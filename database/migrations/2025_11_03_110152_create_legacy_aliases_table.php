<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('legacy_aliases', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('old_reference')->unique();
            $table->foreignId('reference_id')->constrained('references')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legacy_aliases');
    }
};
