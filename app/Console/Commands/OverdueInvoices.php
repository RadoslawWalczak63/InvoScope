<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Console\Command;

class OverdueInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updated status for invoices that are overdue';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = Invoice::query()
            ->where('status', InvoiceStatus::SENT)
            ->where('due_date', '<', now())
            ->update(['status' => InvoiceStatus::OVERDUE]);

        $this->info("Updated {$count} invoices to OVERDUE status.");

        return self::SUCCESS;
    }
}
