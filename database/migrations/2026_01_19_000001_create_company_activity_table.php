<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_activity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->uuid('activity_classificator_id');
            $table->foreign('activity_classificator_id')
                  ->references('id')
                  ->on('activity_classificators')
                  ->onDelete('cascade');
            $table->timestamps();

            // Ensure unique combinations
            $table->unique(['company_id', 'activity_classificator_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_activity');
    }
};
