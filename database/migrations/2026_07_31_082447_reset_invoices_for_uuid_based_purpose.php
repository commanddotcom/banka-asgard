<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const START_AT = 120000;

    /**
     * Run the migrations. Requested cleanup after switching the payment
     * reference from the sequential id to a uuid — invoice_comments is
     * truncated first (FK checks disabled around it) since it references
     * invoices and MySQL won't TRUNCATE a table that's still referenced.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('invoice_comments')->truncate();
        DB::table('invoices')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::statement('ALTER TABLE invoices AUTO_INCREMENT = '.self::START_AT);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('invoice_comments')->truncate();
        DB::table('invoices')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::statement('ALTER TABLE invoices AUTO_INCREMENT = 1');
    }
};
