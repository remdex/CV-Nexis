<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable()->index();
            $table->integer('code')->nullable()->unique();
            $table->string('name');
            $table->string('county_external_id')->nullable()->index();
            $table->date('valid_from')->nullable();
            $table->string('type')->nullable();
            $table->string('type_short')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('municipalities');
    }
};
