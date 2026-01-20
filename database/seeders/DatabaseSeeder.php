<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed competences and specialities based on application locale
        $locale = app()->getLocale() ?: config('app.locale');

        $competences_en = [
            'Nutrition','Food Preparation','Food Sanitation','Fashion Design','Adobe Illustrator','Design Patterns','Travel Consulting','Logistical Planning','UX Design','Adobe Suite','HTML5/CSS','Warehouse Sanitation','Picking/Packing','Adobe Premiere','Video Production','Sales','Safety Compliance','Investigation Skills','Microsoft Excel','CNC Machines','Project Management'
        ];

        $specialities_en = [
            'Nutrition Consultant','Fashion Designer','Travel Agent','UX Designer','Amazon Associate','Inventory Assistant','Video Assistant','Personal Trainer','Security Guard','Accountant','Administrator','Waiter','Driver-Expeditor','Warehouse Worker','CNC Operator','Production Foreman','Vocational Teacher'
        ];

        $competences_lt = [
            'Mitybos mokslas','Maisto gaminimas','Mados dizainas','Adobe Illustrator','Kelionių konsultavimas','Logistikos planavimas','UX dizainas','Programavimo pagrindai','Prekių krovimas','Sandėlio sanitarija','Vaizdo montavimas','Pardavimo įgūdžiai','Saugos atitiktis','Tyrimų įgūdžiai','Microsoft Excel','Rivilė programa','CNC staklės','Projektų valdymas'
        ];

        $specialities_lt = [
            'Mitybos konsultantas','Mados dizaineris','Kelionių agentas','UX dizaineris','Sandėlio darbuotojas','Atsargų padėjėjas','Vaizdo asistentas','Asmeninis treneris','Apsaugos darbuotojas','Apskaitininkas','Administratorius','Padavėjas','Vairuotojas-ekspeditorius','Sandėlininkas','Lazerio operatorius','Gamybos meistras','Profesijos mokytojas'
        ];

        $now = now();

        if ($locale === 'lt' || str_starts_with($locale, 'lt')) {
            $this->seedTable('competences', $competences_lt, $now);
            $this->seedTable('specialities', $specialities_lt, $now);
        } else {
            $this->seedTable('competences', $competences_en, $now);
            $this->seedTable('specialities', $specialities_en, $now);
        }
    }

    /**
     * Insert names into a table if they don't already exist.
     */
    protected function seedTable(string $table, array $names, $timestamp): void
    {
        $rows = array_map(function ($name) use ($timestamp) {
            return [
                'name' => $name,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }, $names);

        // Use insertOrIgnore to avoid duplicate key errors on re-seed
        DB::table($table)->insertOrIgnore($rows);
    }
}
