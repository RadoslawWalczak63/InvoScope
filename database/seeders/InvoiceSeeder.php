<?php

namespace Database\Seeders;

use App\Models\Entity;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if ($user->entities()->count() < 2) {
            Entity::factory()->count(10)->for($user)->create();
        }

        $entities = $user->entities;

        Invoice::factory()
            ->count(50)
            ->for($user)
            ->make()
            ->each(function (Invoice $invoice) use ($entities) {
                $buyer = $entities->random();
                $seller = $entities->where('id', '!=', $buyer->id)->random();

                $invoice->buyer_id = $buyer->id;
                $invoice->seller_id = $seller->id;
                $invoice->save();

                InvoiceItem::factory()
                    ->count(rand(1, 5))
                    ->for($invoice)
                    ->create();
            });
    }
}
