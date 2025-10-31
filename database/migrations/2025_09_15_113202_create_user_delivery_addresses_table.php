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
        Schema::create('user_delivery_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('is_default')->default(0);
            $table->tinyInteger('is_link')->default(0);
            $table->string('short_address')->nullable();
            $table->string('building_number')->nullable();
            $table->string('secondary_number')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Kingdom of Saudi Arabia');
            $table->string('address_link')->nullable();
            $table->string('ar_short_address')->nullable();
            $table->string('ar_building_number')->nullable();
            $table->string('ar_secondary_number')->nullable();
            $table->string('ar_postal_code')->nullable();
            $table->string('ar_city')->nullable();
            $table->string('ar_country')->default('المملكة العربية السعودية');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_delivery_addresses');
    }
};
