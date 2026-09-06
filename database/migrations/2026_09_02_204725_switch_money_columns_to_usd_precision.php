<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Los montos monetarios ahora se almacenan en USD. Un precio unitario en dólares
 * (sobre todo por gramo, ej: $0.0130/g) necesita más decimales que decimal(12,2).
 * Se amplían las columnas de precio/costo unitario a decimal(14,4).
 * Los totales de cabecera se mantienen en decimal(12,2) (USD con 2 decimales basta).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost', 14, 4)->default(0)->change();
            $table->decimal('price', 14, 4)->default(0)->change();
        });

        Schema::table('bulks', function (Blueprint $table) {
            $table->decimal('purchase_price', 14, 4)->default(0)->change();
            $table->decimal('sale_price', 14, 4)->default(0)->change();
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->decimal('unit_price', 14, 4)->change();
            $table->decimal('unit_cost', 14, 4)->nullable()->change();
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->decimal('unit_cost', 14, 4)->change();
            $table->decimal('previous_cost', 14, 4)->nullable()->change();
            $table->decimal('new_cost', 14, 4)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost', 12, 2)->default(0)->change();
            $table->decimal('price', 12, 2)->default(0)->change();
        });

        Schema::table('bulks', function (Blueprint $table) {
            $table->decimal('purchase_price', 12, 2)->default(0)->change();
            $table->decimal('sale_price', 12, 2)->default(0)->change();
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->decimal('unit_price', 12, 2)->change();
            $table->decimal('unit_cost', 12, 2)->nullable()->change();
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->decimal('unit_cost', 12, 2)->change();
            $table->decimal('previous_cost', 12, 2)->nullable()->change();
            $table->decimal('new_cost', 12, 2)->nullable()->change();
        });
    }
};
