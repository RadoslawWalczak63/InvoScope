<?php

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Models\Entity;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrFail();

        if ($user->entities()->count() < 8) {
            Entity::factory()->count(12)->for($user)->create();
        }

        $entities = $user->entities;

        foreach (range(1, 12) as $monthsAgo) {

            $invoiceCount = rand(2, 6) + (12 - $monthsAgo);

            Invoice::factory()
                ->count($invoiceCount)
                ->for($user)
                ->make()
                ->each(function (Invoice $invoice) use ($entities, $monthsAgo) {
                    $buyer = $entities->random();
                    $seller = $entities->where('id', '!=', $buyer->id)->random();

                    $issueDate = now()->subMonths($monthsAgo)->addDays(rand(1, 25));

                    $invoice->buyer_id = $buyer->id;
                    $invoice->seller_id = $seller->id;
                    $invoice->issue_date = $issueDate;
                    $invoice->due_date = (clone $issueDate)->addDays(14);

                    if (rand(1, 100) <= 75) {
                        $invoice->status = InvoiceStatus::PAID;
                        $invoice->paid_date = (clone $issueDate)->addDays(rand(1, 14));
                    } elseif (rand(1, 100) <= 90) {
                        $invoice->status = InvoiceStatus::SENT;
                    } else {
                        $invoice->status = InvoiceStatus::OVERDUE;
                    }

                    $invoice->save();

                    InvoiceItem::factory()
                        ->count(rand(1, 5))
                        ->for($invoice)
                        ->create();
                });
        }
    }
}
