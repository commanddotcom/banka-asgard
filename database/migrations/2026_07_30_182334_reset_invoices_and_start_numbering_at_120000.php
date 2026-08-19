<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const START_AT = 120000;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('invoices')->truncate();
        DB::statement('ALTER TABLE invoices AUTO_INCREMENT = '.self::START_AT);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('invoices')->truncate();
        DB::statement('ALTER TABLE invoices AUTO_INCREMENT = 1');
    }
};
