<?php

use App\Enums\InvoiceType;
use App\Models\Client;
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
                InvoiceType::Expense->value,
                InvoiceType::Income->value,
            ]);
            $table->foreignIdFor(Client::class, 'buyer_id')->constrained();
            $table->foreignIdFor(Client::class, 'seller_id')->constrained();
            $table->date('issue_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
