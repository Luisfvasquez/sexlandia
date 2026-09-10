<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // `MODIFY COLUMN ... ENUM` es sintaxis exclusiva de MySQL. En sqlite
        // (usado en la suite de tests) la columna `status` ya es un string libre.
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'ready_for_pickup', 'ready_for_delivery', 'in_transit', 'completed', 'delivered', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'ready_for_pickup', 'completed', 'delivered', 'cancelled') DEFAULT 'pending'");
    }
};
