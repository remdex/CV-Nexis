<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Orchid\Attachment\Models\Attachment;

class AttachmentClear extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'attachment:clear';

    /**
     * The console command description.
     */
    protected $description = 'Remove dont relation attachment';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $query = Attachment::doesntHave('relationships')
            ->whereDate('created_at', '<', now()->subDays(2));

        $this->info('SQL: ' . $query->toSql());
        $this->info('Bindings: ' . json_encode($query->getBindings()));

        $unrelatedAttachments = $query->get();

        $unrelatedAttachments->each->delete();

        return Command::SUCCESS;
    }
}
