<?php

namespace App\Console\Commands;

use App\Models\QueuedJob;
use App\Services\OpenAiService;
use Illuminate\Console\Command;

class ProcessInvoiceImport extends Command
{
    protected $signature = 'invoice:process {--queuedJobId=}';

    protected $description = 'Process invoice import';

    private ?QueuedJob $queuedJob = null;

    private ?OpenAiService $openAiService = null;

    public function handle(): void
    {
        $this->openAiService = app(OpenAiService::class);

        $this->queuedJob = QueuedJob::findOrFail($this->option('queuedJobId'));
        $this->queuedJob->started();

        // TODO: Implement invoice import logic here

        $this->queuedJob->finished();
        $this->queuedJob->addLog(
            'Invoice imported in '.
            $this->queuedJob->finished_at->diffForHumans(
                $this->queuedJob->started_at,
                short: true,
                parts: 3,
            ),
        );
    }
}
