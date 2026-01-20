<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActivityClassificator;
use Illuminate\Support\Facades\File;

class ImportActivityClassificators extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:activities {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import activity classificators from JSON file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file') ?? storage_path('app/activities.json');

        if (!File::exists($filePath)) {
            $this->error("File not found: {$filePath}");
            $this->info("Please place your JSON file at: storage/app/activities.json");
            $this->info("Or specify a custom path: php artisan import:activities /path/to/file.json");
            return 1;
        }

        $this->info("Reading file: {$filePath}");
        
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON file: ' . json_last_error_msg());
            return 1;
        }

        if (!isset($data['_data']) || !is_array($data['_data'])) {
            $this->error('JSON file must contain "_data" array');
            return 1;
        }

        $imported = 0;
        $updated = 0;
        $skipped = 0;

        $progressBar = $this->output->createProgressBar(count($data['_data']));
        $progressBar->start();

        foreach ($data['_data'] as $item) {
            try {
                $activityData = [
                    'id' => $item['_id'] ?? null,
                    'code' => $item['kodas'] ?? null,
                    'name_lt' => $item['pavadinimas_lt'] ?? null,
                    'name_en' => $item['pavadinimas_en'] ?? null,
                    'notes_lt' => $item['pastabos_lt'] ?? null,
                    'notes_en' => $item['pastabos_en'] ?? null,
                    'level' => $item['lygmuo'] ?? null,
                    'broader_activity_type' => $item['bendresne_veiklos_rusis'] ?? null,
                ];

                if (empty($activityData['id'])) {
                    $this->warn("Skipping item without _id");
                    $skipped++;
                    continue;
                }

                $activity = ActivityClassificator::updateOrCreate(
                    ['id' => $activityData['id']],
                    $activityData
                );

                if ($activity->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $updated++;
                }

            } catch (\Exception $e) {
                $this->error("Error processing item: " . $e->getMessage());
                $skipped++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Import completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Imported', $imported],
                ['Updated', $updated],
                ['Skipped', $skipped],
                ['Total', $imported + $updated + $skipped],
            ]
        );

        return 0;
    }
}
