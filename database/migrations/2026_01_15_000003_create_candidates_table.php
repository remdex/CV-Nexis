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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('active')->default(true);
            $table->boolean('black_list')->default(false);
            $table->boolean('locked')->default(false);
            $table->foreignId('locked_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('comment')->nullable();
            $table->string('city')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('speciality_entered_manually')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
