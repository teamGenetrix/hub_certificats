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
        Schema::create('references', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->enum('doc_kind', ['CER', 'ATT']);
            $table->string('reference')->unique();
            $table->char('month_year', 6);
            $table->unsignedInteger('global_increment');
            $table->unsignedInteger('pillar_increment');
            $table->foreignId('pillar_id')->constrained('pillars')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('enrollment_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('month_year', 'idx_refs_month_year');
            $table->index(['doc_kind', 'month_year'], 'idx_refs_kind_month');
            $table->index(['pillar_id', 'month_year'], 'idx_refs_pillar_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('references');
    }
};
