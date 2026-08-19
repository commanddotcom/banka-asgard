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
        Schema::table('donors', function (Blueprint $table) {
            $table->string('phone_last4', 4)->after('name');
        });

        // The old composite unique is the only index covering bank_id, which
        // the FK constraint needs — add the replacement before dropping it,
        // so there's never a moment with no index supporting that FK.
        Schema::table('donors', function (Blueprint $table) {
            $table->unique(['bank_id', 'period', 'name', 'phone_last4']);
            $table->dropUnique(['bank_id', 'period', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            $table->unique(['bank_id', 'period', 'name']);
            $table->dropUnique(['bank_id', 'period', 'name', 'phone_last4']);
        });

        Schema::table('donors', function (Blueprint $table) {
            $table->dropColumn('phone_last4');
        });
    }
};
