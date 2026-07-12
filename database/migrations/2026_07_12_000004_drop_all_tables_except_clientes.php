<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Resets the database schema while preserving the `clientes` table.
 *
 * This migration drops every table in the database except:
 *  - `clientes` (must be preserved, contains client data that should survive
 *    the reset)
 *  - `migrations` (Laravel's own migration registry, required for the
 *    migration system to keep functioning)
 *  - `password_reset_tokens` (kept alongside the users table structure so
 *    the migrate:fresh run below can recreate related tables cleanly)
 *
 * After this migration runs, the caller is expected to run
 * `php artisan migrate:fresh --seed`... however, since `migrate:fresh` drops
 * ALL tables (including `clientes`), the intended workflow is instead:
 *
 *   php artisan migrate --path=database/migrations/2026_07_12_000004_drop_all_tables_except_clientes.php
 *   php artisan migrate --seed
 *
 * so that only the non-`clientes` tables are dropped here, and then a normal
 * `migrate` run recreates them (with seeders populating fresh data) without
 * touching `clientes`.
 */
return new class extends Migration
{
    /**
     * Tables that must NOT be dropped.
     *
     * @var array<int, string>
     */
    private array $preservedTables = [
        'clientes',
        'migrations',
        'password_reset_tokens',
    ];

    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $database = DB::getDatabaseName();

        $tables = DB::select(
            'SELECT table_name AS table_name FROM information_schema.tables WHERE table_schema = ?',
            [$database]
        );

        foreach ($tables as $table) {
            $tableName = $table->table_name;

            if (in_array($tableName, $this->preservedTables, true)) {
                continue;
            }

            DB::statement("DROP TABLE IF EXISTS `{$tableName}`;");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        // Intentionally left blank. Dropping tables is a destructive,
        // one-way operation — there is nothing meaningful to reverse here.
        // Restoring the schema is handled by re-running the normal
        // migrations (and seeders) afterwards.
    }
};
