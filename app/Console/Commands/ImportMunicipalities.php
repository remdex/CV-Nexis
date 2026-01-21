<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Municipality;

class ImportMunicipalities extends Command
{
    protected $signature = 'import:municipalities {file?}';

    protected $description = 'Import municipalities from JSON file';

    public function handle()
    {
        $filePath = $this->argument('file') ?? storage_path('app/municipality.json');

        if (!\Illuminate\Support\Facades\File::exists($filePath)) {
            $this->error("File not found: {$filePath}");
            $this->info("Please place your JSON file at: storage/app/municipality.json");
            $this->info("Or specify a custom path: php artisan import:municipalities /path/to/file.json");
            return 1;
        }

        $this->info("Reading file: {$filePath}");
        $content = \Illuminate\Support\Facades\File::get($filePath);
        $json = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON file: ' . json_last_error_msg());
            return 1;
        }

        if (empty($json['_data']) || !is_array($json['_data'])) {
            $this->error('Invalid JSON structure in file');
            return 1;
        }

        $imported = 0;
        $updated = 0;
        $skipped = 0;

        $progressBar = $this->output->createProgressBar(count($json['_data']));
        $progressBar->start();

        foreach ($json['_data'] as $item) {
            try {
                $externalId = $item['_id'] ?? null;
                $code = $item['sav_kodas'] ?? null;
                $name = $item['pavadinimas'] ?? null;
                $countyExternal = $item['apskritis']['_id'] ?? null;
                $validFrom = $item['sav_nuo'] ?? null;
                $type = $item['tipas'] ?? null;
                $typeShort = $item['tipo_santrumpa'] ?? null;

                if (empty($code) || empty($name)) {
                    $skipped++;
                    continue;
                }

                $municipality = Municipality::updateOrCreate(
                    ['code' => $code],
                    [
                        'external_id' => $externalId,
                        'name' => $name,
                        'county_external_id' => $countyExternal,
                        'valid_from' => $validFrom,
                        'type' => $type,
                        'type_short' => $typeShort,
                    ]
                );

                if ($municipality->wasRecentlyCreated) {
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

        $this->info('Import completed!');
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
