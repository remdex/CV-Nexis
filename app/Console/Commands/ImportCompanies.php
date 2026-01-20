<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:companies {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import companies from CSV file (supports large files)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file') ?? storage_path('app/companies.csv');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            $this->info("Please place your CSV file at: storage/app/companies.csv");
            $this->info("Or specify a custom path: php artisan import:companies /path/to/file.csv");
            return 1;
        }

        $this->info("Starting import from: {$filePath}");
        $this->info("File size: " . $this->formatBytes(filesize($filePath)));

        // Disable query logging to save memory
        DB::connection()->disableQueryLog();
        
        $startTime = microtime(true);
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            $this->error("Could not open file: {$filePath}");
            return 1;
        }

        // Read header row
        $headers = fgetcsv($handle);
        
        if ($headers === false) {
            $this->error("Could not read CSV headers");
            fclose($handle);
            return 1;
        }

        $this->info("CSV Headers detected: " . count($headers) . " columns");

        // Create mapping of CSV columns to array indices
        $headerMap = array_flip($headers);

        $batchSize = 250; // Reduced batch size for memory efficiency
        $batch = [];
        $totalProcessed = 0;
        $totalInserted = 0;
        $totalSkipped = 0;
        $lineNumber = 1; // Header is line 1

        $bar = $this->output->createProgressBar();
        $bar->setFormat('verbose');

        while (($row = fgetcsv($handle)) !== false) {
            $lineNumber++;

            try {
                // Skip rows without company_code
                if (!isset($headerMap['ja_kodas']) || empty($row[$headerMap['ja_kodas']])) {
                    $totalSkipped++;
                    continue;
                }

                if (!empty($this->getValue($row, $headerMap, 'veiklos_pabaiga'))){
                     $totalSkipped++;
                     continue;
                }

                $activityClassificatorId = $this->getValue($row, $headerMap, 'ekonomine_veikla._id');

                $company = [
                    'company_code' => $this->getValue($row, $headerMap, 'ja_kodas'),
                    'name' => $this->getValue($row, $headerMap, 'pavadinimas'),
                    'client_type' => $this->getValue($row, $headerMap, 'klnt_tipas'),
                    'registration_date' => $this->parseDate($this->getValue($row, $headerMap, 'ireg_data')),
                    'deregistration_date' => $this->parseDate($this->getValue($row, $headerMap, 'isreg_data')),
                    'annulment_date' => $this->parseDate($this->getValue($row, $headerMap, 'anul_data')),
                    'country' => $this->getValue($row, $headerMap, 'valstybe'),
                    'type_code' => $this->getValue($row, $headerMap, 'tipo_kodas'),
                    'type_description' => $this->getValue($row, $headerMap, 'tipo_aprasymas'),
                    'type_from_date' => $this->parseDate($this->getValue($row, $headerMap, 'tipas_nuo')),
                    'type_until_date' => $this->parseDate($this->getValue($row, $headerMap, 'tipas_iki')),
                    'annulment_type' => $this->getValue($row, $headerMap, 'anul_tipas'),
                    'vat_code_prefix' => $this->getValue($row, $headerMap, 'pvm_kodas_pref'),
                    'vat_code' => $this->getValue($row, $headerMap, 'pvm_kodas'),
                    'vat_registered_date' => $this->parseDate($this->getValue($row, $headerMap, 'pvm_iregistruota')),
                    'vat_deregistered_date' => $this->parseDate($this->getValue($row, $headerMap, 'pvm_isregistruota')),
                    'division_number' => $this->getValue($row, $headerMap, 'padalinio_nr'),
                    'division_name' => $this->getValue($row, $headerMap, 'padalinio_pvd'),
                    'division_municipality' => $this->getValue($row, $headerMap, 'padalinio_savivaldybe'),
                    'division_code' => $this->getValue($row, $headerMap, 'padalinio_kodas'),
                    'formed_date' => $this->parseDate($this->getValue($row, $headerMap, 'suformuota')),
                    'deformed_date' => $this->parseDate($this->getValue($row, $headerMap, 'isformuota')),
                    'activity_start_date' => $this->parseDate($this->getValue($row, $headerMap, 'veiklos_pradzia')),
                    'activity_end_date' => $this->parseDate($this->getValue($row, $headerMap, 'veiklos_pabaiga')),
                    'created_at' => now(),
                    'updated_at' => now(),
                    '_activity_classificator_id' => $activityClassificatorId, // Store temporarily for later processing
                ];

                $batch[] = $company;
                $totalProcessed++;

                // Insert batch when it reaches the batch size
                if (count($batch) >= $batchSize) {
                    $this->insertBatch($batch);
                    $totalInserted += count($batch);
                    $batch = [];
                    $bar->advance($batchSize);
                    
                    // Free memory every batch
                    if ($totalProcessed % 5000 == 0) {
                        gc_collect_cycles();
                    }
                }

            } catch (\Exception $e) {
                $this->error("\nError on line {$lineNumber}: " . $e->getMessage());
            }
        }

        // Insert remaining records
        if (!empty($batch)) {
            $this->insertBatch($batch);
            $totalInserted += count($batch);
            $bar->advance(count($batch));
        }

        fclose($handle);
        $bar->finish();
        
        // After all batches inserted, remove unused activity classificators via SQL
        $this->info("\nRemoving unused activity_classificators via SQL...");
        $this->cleanupUnusedActivityClassificators();
        $this->info("Removal completed.");
        // Re-enable query logging
        DB::connection()->enableQueryLog();

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);

        $this->newLine(2);
        $this->info("Import completed successfully!");
        $this->info("Total rows in CSV: " . ($lineNumber - 1));
        $this->info("Total processed: {$totalProcessed} records");
        $this->info("Total inserted/updated: {$totalInserted} records");
        $this->info("Total skipped (no ja_kodas): {$totalSkipped} records");
        $this->info("Duration: {$duration} seconds");
        $this->info("Average: " . round($totalInserted / $duration, 2) . " records/second");

        return 0;
    }

    /**
     * Get value from row by column name
     */
    private function getValue(array $row, array $headerMap, string $column): ?string
    {
        if (!isset($headerMap[$column])) {
            return null;
        }

        $value = $row[$headerMap[$column]] ?? null;

        return ($value === '' || $value === null) ? null : trim($value);
    }

    /**
     * Parse date string to Y-m-d format
     */
    private function parseDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Insert batch of records using upsert
     */
    private function insertBatch(array $batch): int
    {
        try {
            // Group companies by company_code to handle duplicates in CSV
            $companiesByCode = [];
            $activityMappings = [];
            
            foreach ($batch as $company) {
                $companyCode = $company['company_code'];
                $activityClassificatorId = $company['_activity_classificator_id'] ?? null;
                unset($company['_activity_classificator_id']);
                
                // Store or update company data (last occurrence wins for company fields)
                $companiesByCode[$companyCode] = $company;
                
                // Collect all activity mappings for this company
                if ($activityClassificatorId) {
                    $activityMappings[] = [
                        'company_code' => $companyCode,
                        'activity_classificator_id' => $activityClassificatorId,
                    ];
                }
            }
            
            // Use upsert to insert or update existing company records
            if (!empty($companiesByCode)) {
                DB::table('companies')->upsert(
                    array_values($companiesByCode),
                    ['company_code'], // Unique key
                    array_keys(reset($companiesByCode)) // Update all columns on conflict
                );
            }
            
            // Insert activity relationships into pivot table
            if (!empty($activityMappings)) {
                
                $pivotRecords = [];
                
                // Get all company IDs in one query for efficiency
                $companyCodes = array_unique(array_column($activityMappings, 'company_code'));
                $companyIds = DB::table('companies')
                    ->whereIn('company_code', $companyCodes)
                    ->pluck('id', 'company_code');
                
                foreach ($activityMappings as $mapping) {
                    $companyId = $companyIds[$mapping['company_code']] ?? null;
                    
                    if ($companyId) {
                        $pivotRecords[] = [
                            'company_id' => $companyId,
                            'activity_classificator_id' => $mapping['activity_classificator_id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                
                if (!empty($pivotRecords)) {
                    // Use upsert to avoid duplicates - this will add new activities without removing old ones
                    DB::table('company_activity')->upsert(
                        $pivotRecords,
                        ['company_id', 'activity_classificator_id'],
                        ['updated_at']
                    );
                }
            }
            
            return count($batch);
        } catch (\Exception $e) {
            $this->error("\nBatch insert error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Remove unreferenced activity_classificators using SQL for scale.
     */
    private function cleanupUnusedActivityClassificators(): void
    {
        try {
            $deleted = DB::table('activity_classificators')
                ->whereNotIn('id', DB::table('company_activity')->select('activity_classificator_id'))
                ->delete();

            $this->info("Deleted {$deleted} unused activity_classificators.");
        } catch (\Exception $e) {
            $this->error('Error deleting unused activity_classificators: ' . $e->getMessage());
        }
    }

    /**
     * Format bytes to human readable size
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
