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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('code')->unique();
            $table->string('title');
            $table->boolean('has_exam')->default(false);
            $table->boolean('is_custom')->default(false);
            $table->string('custom_code')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreignId('pillar_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
