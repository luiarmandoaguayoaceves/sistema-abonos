<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dateTime('fecha_entrega')->nullable()->after('total');
        });

        DB::table('pedidos')->update([
            'fecha_entrega' => DB::raw('created_at'),
        ]);
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn('fecha_entrega');
        });
    }
};