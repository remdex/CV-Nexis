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
        if (!Schema::hasTable('activity_classificators')) {
            Schema::create('activity_classificators', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('code')->nullable();
                $table->string('name_lt')->nullable();
                $table->string('name_en')->nullable();
                $table->text('notes_lt')->nullable();
                $table->text('notes_en')->nullable();
                $table->string('level')->nullable();
                $table->string('broader_activity_type')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_classificators');
    }
};
