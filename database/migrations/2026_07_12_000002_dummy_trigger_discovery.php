<?php

use Illuminate\Database\Migrations\Migration;

/**
 * This is an intentionally empty "no-op" migration.
 *
 * It exists solely to force Laravel's `migrate` command to re-scan the
 * migrations directory and pick up the
 * `2026_07_11_000001_remove_detalles_json_from_pedidos_table` migration,
 * which was added to the repository after the migration registry for the
 * previous deploy had already been established. Without a newer migration
 * file being introduced, `php artisan migrate` reported "Nothing to
 * migrate" and skipped the pending migration.
 *
 * This migration can be safely removed once the target migration has run
 * successfully in every environment.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Intentionally left blank.
    }

    public function down(): void
    {
        // Intentionally left blank.
    }
};
