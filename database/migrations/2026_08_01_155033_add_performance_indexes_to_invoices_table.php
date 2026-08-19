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
            // Donor-roster aggregation (DonorController) and the scheduled
            // payment checker both filter a single bank's invoices by
            // status, then by a paid_at range or the unpaid scope.
            $table->index(['bank_id', 'status', 'paid_at']);

            // Admin invoices list: filters by status (paid/unpaid), sorted
            // by created_at, with no bank filter — a separate leading
            // column is needed since it doesn't share a prefix with bank_id.
            $table->index(['status', 'created_at']);

            // Donor-roster matching groups/looks up by this exact pair.
            $table->index(['name', 'phone_last4']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['bank_id', 'status', 'paid_at']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['name', 'phone_last4']);
        });
    }
};
