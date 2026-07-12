<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The migration `2026_07_11_000001_remove_detalles_json_from_pedidos_table`
 * was previously registered in the `migrations` table (its row was inserted
 * into the registry) without ever actually being executed against the
 * database. Because Laravel considers a migration "done" as soon as it has
 * a row in the `migrations` table, `php artisan migrate` was skipping it on
 * every subsequent deploy, even after the dummy trigger migration forced a
 * re-scan of the migrations directory.
 *
 * This migration removes that stale registry entry so Laravel treats the
 * target migration as pending again and executes it on the next run.
 *
 * This migration can be safely removed once the target migration has run
 * successfully in every environment.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('migrations')) {
            return;
        }

        DB::table('migrations')
            ->where('migration', '2026_07_11_000001_remove_detalles_json_from_pedidos_table')
            ->delete();
    }

    public function down(): void
    {
        // Intentionally left blank. There is nothing meaningful to reverse:
        // re-inserting the stale registry row would just reintroduce the
        // original problem.
    }
};
