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
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('product_name')->nullable();
            $table->string('ar_product_name')->nullable();
            $table->string('product_price')->nullable();
            $table->string('product_image')->nullable();
            $table->string('product_pieces_per_box')->nullable();
            $table->string('unit')->nullable();
            $table->string('ar_unit')->nullable();
            $table->string('uom_unit')->nullable();
            $table->string('ar_uom_unit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('product_name');
            $table->dropColumn('ar_product_name');
            $table->dropColumn('product_price');
            $table->dropColumn('product_image');
            $table->dropColumn('product_pieces_per_box');
            $table->dropColumn('unit');
            $table->dropColumn('ar_unit');
            $table->dropColumn('uom_unit');
            $table->dropColumn('ar_uom_unit');
        });
    }
};
