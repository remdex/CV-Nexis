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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_code')->comment('Company code');
            $table->string('name')->comment('Company name');
            $table->string('client_type')->nullable()->comment('Client type');
            $table->date('registration_date')->nullable()->comment('Registration date');
            $table->date('deregistration_date')->nullable()->comment('Deregistration date');
            $table->date('annulment_date')->nullable()->comment('Annulment date');
            $table->string('country', 3)->nullable()->comment('Country');
            $table->string('type_code')->nullable()->comment('Type code');
            $table->string('type_description')->nullable()->comment('Type description');
            $table->date('type_from_date')->nullable()->comment('Type from date');
            $table->date('type_until_date')->nullable()->comment('Type until date');
            $table->string('annulment_type')->nullable()->comment('Annulment type');
            $table->string('vat_code_prefix')->nullable()->comment('VAT code prefix');
            $table->string('vat_code')->nullable()->comment('VAT code');
            $table->date('vat_registered_date')->nullable()->comment('VAT registered date');
            $table->date('vat_deregistered_date')->nullable()->comment('VAT deregistered date');
            $table->string('division_number')->nullable()->comment('Division number');
            $table->string('division_name')->nullable()->comment('Division name');
            $table->string('division_municipality')->nullable()->comment('Division municipality');
            $table->string('division_code')->nullable()->comment('Division code');
            $table->date('activity_start_date')->nullable()->comment('Activity start date');
            $table->date('activity_end_date')->nullable()->comment('Activity end date');
            $table->date('formed_date')->nullable()->comment('Formed date');
            $table->date('deformed_date')->nullable()->comment('Deformed date');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique('company_code', 'companies_company_code_unique');
            $table->index('company_code', 'companies_company_code_index');
            $table->index('name', 'companies_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
