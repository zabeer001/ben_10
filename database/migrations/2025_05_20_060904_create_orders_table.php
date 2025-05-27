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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_model_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('theme_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('customer_info_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('base_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('status')->default('pending');

            // Add two color foreign keys from the `colors` table
            $table->foreignId('external_base_colour_id')->nullable()->constrained('colors')->onDelete('cascade');
            $table->foreignId('external_decay_colour_id')->nullable()->constrained('colors')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
