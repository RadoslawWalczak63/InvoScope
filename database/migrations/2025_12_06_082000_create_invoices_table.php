<?php

use App\Enums\InvoiceType;
use App\Models\Entity;
use App\Models\Invoice;
use App\Models\User;
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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->onDelete('cascade');
            $table->string('number')->unique();
            $table->enum('type', [
                InvoiceType::EXPENSE->value,
                InvoiceType::INCOME->value,
            ]);
            $table->foreignIdFor(Entity::class, 'buyer_id')->constrained();
            $table->foreignIdFor(Entity::class, 'seller_id')->constrained();
            $table->date('issue_date');
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Invoice::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('total', 10, 2)->virtualAs('price * quantity');
            $table->string('description')->nullable();
            $table->string('tax')->nullable();
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('net_total', 10, 2)
                ->virtualAs('total + tax_amount - discount');
            $table->string('unit')->default('pcs');
            $table->string('sku')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
