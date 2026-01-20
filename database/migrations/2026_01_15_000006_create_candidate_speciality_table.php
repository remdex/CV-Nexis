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
        if (!Schema::hasTable('candidate_speciality')) {
            Schema::create('candidate_speciality', function (Blueprint $table) {
                $table->id();
                $table->foreignId('candidate_id')->constrained('candidates')->onDelete('cascade');
                $table->foreignId('speciality_id')->constrained('specialities')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_speciality');
    }
};
