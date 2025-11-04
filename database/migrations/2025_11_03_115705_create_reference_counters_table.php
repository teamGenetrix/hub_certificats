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
        Schema::create('reference_counters', function (Blueprint $table) {
            $table->id();
            $table->string('scope_key', 191)->unique();
            $table->unsignedInteger('counter')->default(0);
            $table->timestamps();

            $table->comment('Compteurs atomiques pour la génération de références');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reference_counters');
    }
};
