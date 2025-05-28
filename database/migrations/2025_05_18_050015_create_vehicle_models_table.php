<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sleep_person');
            $table->longText('description')->nullable();
            $table->string('inner_image')->nullable();
                    $table->string('outer_image')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->decimal('base_price', 10, 2); // Base price with 2 decimal places
            $table->decimal('price', 10, 2);      // Current or rental price
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_models');
    }
};
