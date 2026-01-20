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
        if (!Schema::hasTable('candidate_company')) {
            Schema::create('candidate_company', function (Blueprint $table) {
                $table->id();
                $table->foreignId('candidate_id')->constrained('candidates')->onDelete('cascade');
                $table->string('company_code');
                $table->timestamps();

                $table->index('company_code');
                $table->unique(['candidate_id', 'company_code']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_company');
    }
};
