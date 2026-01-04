<?php

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
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('issue_date');
            $table->date('paid_date')->nullable()->after('due_date');
        });

        DB::table('invoices')->update([
            'due_date' => DB::raw('DATE_ADD(issue_date, INTERVAL 30 DAY)'),
        ]);

        Schema::table('invoices', function (Blueprint $table) {
            $table->date('due_date')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('due_date');
            $table->dropColumn('paid_date');
        });
    }
};
